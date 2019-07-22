<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMutasi extends mModel {
		const schema = 'aset';
		const table = 'as_mutasi';
		const order = 'idmutasi desc';
		const key = 'idmutasi';
		const label = 'Mutasi';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idmutasi,u.namaunit,t.namaunit as namaunittujuan,tglpengajuan,status,isverify,isok1,
				idunitasal,idlokasiasal,idunittujuan,idlokasitujuan
                from ".self::table()." m 
				left join ".static::schema.".ms_unit u on u.idunit = m.idunitasal 
				left join ".static::schema.".ms_unit t on t.idunit = m.idunittujuan";
			
			return $sql;
		}
		
		function dataQuery($key){
		    $sql = "select idmutasi,idunitasal,idlokasiasal,tglmutasi,tglpengajuan,status,
                isverify,verifynote,isok1,memo1,idunittujuan,idlokasitujuan,idpegawaitujuan,p.namalengkap as pegawaitujuan,
				insertuser, inserttime, verifyuser, verifytime, idpegawaiasal, p2.namalengkap as pegawaiasal,m.catatan,
				m.idlokasitujuan+' - '+lt.namalokasi as lokasitujuan
                from ".self::table()." m 
				left join sdm.v_biodatapegawai p on p.idpegawai = m.idpegawaitujuan
				left join sdm.v_biodatapegawai p2 on p2.idpegawai = m.idpegawaiasal
				left join aset.ms_lokasi lt on lt.idlokasi = m.idlokasitujuan 
				where ".static::getCondition($key);
				
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key,$tahun='',$bulan='') {
			switch($col) {
				case 'periode': 
					return "datepart(year,tglpengajuan) = '$tahun' and datepart(month,tglpengajuan) = '$bulan' "; 
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
		    $sql = "select isok1,isverify,idunitasal,idlokasiasal,idpegawaiasal,idunittujuan,idlokasitujuan,idpegawaitujuan,tglmutasi,tglpengajuan
		        from ".self::table()." where idmutasi = '$key'";
		    return $conn->GetRow($sql);
		}
		
		function setMutasi($conn, $key){
	        $sql = "update s set
	                idunit = m.idunittujuan,
	                idlokasi = m.idlokasitujuan,
	                idpegawai = m.idpegawaitujuan 
	            from aset.as_seri s
	                join aset.as_mutasidetail d on d.idseri = s.idseri  
	                join aset.as_mutasi m on m.idmutasi = d.idmutasi 
	            where m.idmutasi = '$key'";
            return $conn->Execute($sql);
		}

		function setBatal($conn, $key){
	        $sql = "update s set
	                idunit = m.idunitasal,
	                idlokasi = m.idlokasiasal,
	                idpegawai = m.idpegawaiasal 
	            from aset.as_seri s
	                join aset.as_mutasidetail d on d.idseri = s.idseri  
	                join aset.as_mutasi m on m.idmutasi = d.idmutasi 
	            where m.idmutasi = '$key'";
	        return $conn->Execute($sql);
		}
		
		function mailMutasi($conn, $key) {
			global $conf;

		    require_once(Route::getModelPath('mutasidetail'));
		    $p_modeldet = mMutasiDetail;
		    
		    $a_mdata = self::getMData($conn, $key);
		    
		    /*if(!empty($a_mdata['insertuserid']))
    		    $tujuan = $conn->GetOne("select email from sdm.ms_pegawai where idpegawai = '{$a_mdata['insertuserid']}'");*/
		    $tujuan = 'amirul_muminin1987@yahoo.co.id';
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
			    $isi.='	 <td colspan="2">Sehubungan dengan adanya pengajuan mutasi aset pada tanggal <b>'.Cstr::formatDateInd($a_mdata['tglpengajuan']).'</b> dengan rincian aset sebagai berikut:</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2">&nbsp;</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td colspan="2" class="highlights"><b>RUANGAN ASAL</b></td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Unit</td>';
				$isi.='  <td>: '.$a_data['unitasal'].'</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Pemakai Asal</td>';
				$isi.='  <td>: '.$a_data['pegawaiasal'].'</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td colspan="2" class="highlights"><b>RUANGAN TUJUAN</b></td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Unit</td>';
				$isi.='  <td>: '.$a_data['unittujuan'].'</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Pemakai Tujuan</td>';
				$isi.='  <td>: '.$a_data['namapegawai'].'</td>';
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
				$isi.='             <td align="center" width="100">Kondisi</td>';
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
							$isi.='  <td valign="top">'.$val['kondisi'].'</td>';
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
			    $isi.='	 <td colspan="2"><b>Dimohon tindak lanjut dari pengajuan mutasi tersebut untuk diproses di sistem SIM Aset sebagaimana mestinya. Terima Kasih</b></td>';
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
			    
			    $subject = 'Pengajuan Mutasi Aset tgl. '.Cstr::formatDateInd($a_mdata['tglpengajuan']);
			
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
