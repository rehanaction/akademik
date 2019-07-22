<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPenghapusan extends mModel {
		const schema = 'aset';
		const table = 'as_penghapusan';
		const order = 'idpenghapusan desc';
		const key = 'idpenghapusan';
		const label = 'Penghapusan';

		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idpenghapusan,namaunit,tglpenghapusan,jenispenghapusan,p.idjenispenghapusan,
					nobukti,isok1,isverify,status,tglpengajuan,catatan
					from ".self::table()." p 
					left join ".static::schema.".ms_unit u on u.idunit = p.idunit
					left join ".static::schema.".ms_jenispenghapusan j on j.idjenispenghapusan = p.idjenispenghapusan";
			
			return $sql;
		}

		function dataQuery($key){
		    $sql = "select idpenghapusan,idjenispenghapusan,p.idunit,tglpengajuan,status,
                isverify,verifynote,isok1,memo1,tglpenghapusan,p.catatan,
				insertuser, inserttime, verifyuser, verifytime, p.idlokasi
                from ".self::table()." p 
				left join aset.ms_lokasi l on l.idlokasi = p.idlokasi
				where ".static::getCondition($key);
				
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key,$tahun='',$bulan='') {
		if(!empty($bulan))
			$bln = "and datepart(month,tglpengajuan) = '$bulan'";

			switch($col) {
				case 'jenispenghapusan': 
				    return "p.idjenispenghapusan = '$key'";
			    break;
				case 'periode': 
					return "datepart(year,tglpengajuan) = '$tahun' $bln "; 
				break;
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				break;
			}
		}
		
		function getMData($conn, $key){
		    return $conn->GetRow("select isok1,isverify,idunit,idjenispenghapusan,tglpenghapusan,tglpengajuan,idlokasi from ".self::table()." where idpenghapusan = '$key'");
		}
		
		function setHapus($conn, $key){
	        $sql = "update aset.as_seri set idstatus = 'X' 
	            where idseri in (select idseri from aset.as_penghapusandetail where idpenghapusan = '$key')";
            return $conn->Execute($sql);
		}

		function setBatal($conn, $key){
		    $sql = "update aset.as_seri set idstatus = 'A' 
		        where idseri in (select idseri from aset.as_penghapusandetail where idpenghapusan = '$key')";
	        return $conn->Execute($sql);
		}
		
		function mailHapus($conn, $key) {
			global $conf;

		    require_once(Route::getModelPath('penghapusandetail'));
		    $p_modeldet = mPenghapusanDetail;
		    
		    $a_mdata = self::getMData($conn, $key);
		    
		    /*if(!empty($a_mdata['insertuserid']))*/
    		    $tujuan = $conn->GetOne("select email from sdm.ms_pegawai where idpegawai = (
											select top 1 idpegawai
											from gate.sc_user u
											join gate.sc_userrole ur on ur.userid = u.userid
											where ur.koderole = 'kadu' and u.username <> 'sup3r') ");
		    //$tujuan = 'amirul_muminin1987@yahoo.co.id';
			echo $tujuan;
            if(!empty($tujuan)){

		        require_once($conf['includes_dir'].'phpmailer/class.phpmailer.php');
		        $det = $p_modeldet::getRowByIDP($conn, $key);
				$a_data = $p_modeldet::getHeaderByIDP($conn, $key);
		        $p_tbwidth = 1000;
		        //isi email
		        $isi ='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			    $isi.=' <tr>';
			    $isi.='	 <td></td>';
			    $isi.='	 <td><font size="+2"><strong>'.$conf['univ_name'].'</strong></font></td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td></td>';
			    $isi.='	 <td>'.$conf['univ_address'].'</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td></td>';
			    $isi.='	 <td>Telp. '.$conf['univ_telp'].', Fax : '.$conf['univ_fax'].'</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td></td>';
			    $isi.='	 <td>'.$conf['univ_email'].'</td>';
			    $isi.=' </tr>';
			    $isi.='</table>';
			
			    $isi.='<hr style="width:"'.$p_tbwidth.'" align="left"><br>';
			    $isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2">Sehubungan dengan adanya pengajuan penghapusan aset pada tanggal <b>'.Cstr::formatDateInd($a_mdata['tglpengajuan']).'</b> dengan rincian aset sebagai berikut:</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2">&nbsp;</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Unit</td>';
				$isi.='  <td>: '.$a_data['namaunit'].'</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Lokasi</td>';
				$isi.='  <td>: '.$a_data['namalokasi'].'</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td colspan="2">&nbsp;</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2"> ';
			    $isi.='     <table border="1" style="border-collapse:collapse;" width="900">';
			    $isi.='         <tr>';
			    $isi.='             <td align="center" width="30">No.</td>';
			    $isi.='             <td align="center" width="60">No. Seri</td>';
				$isi.='             <td align="center">Barang</td>';
				$isi.='             <td align="center" width="100">Merk</td>';
			    $isi.='             <td align="center" width="125">Spesifikasi</td>';
			    $isi.='             <td align="center" width="115">Tgl. Perolehan</td>';
				$isi.='             <td align="center" width="100">Nilai Penghapusan</td>';
			    $isi.='         </tr>';
				
				    if(count($det)>0){
				        $i = 0;
					    foreach($det as $id => $val){
					        $i++;
						    $isi.=' <tr>';
						    $isi.='  <td valign="top">'.$i.'</td>';
							$isi.='  <td valign="top">'.Aset::formatNoSeri($val['noseri']).'</td>';
						    $isi.='  <td valign="top">'.$val['barang'].'</td>';
						    $isi.='  <td valign="top">'.$val['merk'].'</td>';
							$isi.='  <td valign="top">'.$val['spesifikasi'].'</td>';
						    $isi.='  <td valign="top" align="center">'.CStr::formatDateInd($val['tglperolehan']).'</td>';
							$isi.='  <td valign="top">'.$val['nilaipenghapusan'].'</td>';
						    $isi.=' </tr>';
					    }
				    }
				
			    $isi.='     </table>';
			    $isi.='  </td>';
			    $isi.=' </tr>';	
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2">&nbsp;</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2"><b>Dimohon tindak lanjut dari pengajuan penghapusan tersebut untuk diproses di sistem SIM Aset sebagaimana mestinya. Terima Kasih</b></td>';
			    $isi.=' </tr>';
			    $isi.='</table><br>';
			
			    $isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			    $isi.='	<tr>';
			    $isi.='	 <td>';
			    $isi.='	    <a href="http://simueu.esaunggul.ac.id">http://simueu.esaunggul.ac.id</a>';
			    $isi.='	 </td>';
			    $isi.='</tr>';
			    $isi.='</table>';
			
			    echo $isi;
			    
			    $subject = 'Pengajuan Penghapusan Aset tgl. '.Cstr::formatDateInd($a_mdata['tglpengajuan']);
			
		        $mail = new PHPMailer;
		        $mail->IsSMTP();  // send via SMTP
		        try {
			        $mail->IsHTML(true);
			        //$mail->SMTPAuth = true;
			        $mail->Host     = $conf['smtp_host']; // SMTP servers
			        $mail->Username = $conf['smtp_user'];
			        $mail->Password = $conf['smtp_pass'];
			        $mail->ClearAddresses();
			        $mail->AddAddress($tujuan);
			        $mail->From = $conf['smtp_email'];
			        $mail->FromName = $conf['smtp_admin'];
			        $mail->Subject = $subject;
			        $mail->Body = $isi;			
			        $mail->Send();
			
			        return true;
		        } catch (phpmailerException $e) {
			        return false;
		        } catch (Exception $e) {
			        return false;
		        }
			    
		    }
		}
		
	}
?>
