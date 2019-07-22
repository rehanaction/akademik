<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPinjam extends mModel {
		const schema = 'aset';
		const table = 'as_pinjam';
		const order = 'idpinjam desc';
		const key = 'idpinjam';
		const label = 'peminjaman';
		
        // mendapatkan kueri list
		function listQuery() {
			$sql = "select idpinjam,tglpengajuan,tglpinjam,tgltenggat,status,isverify,isok1,
			    u.kodeunit+' - '+u.namaunit as unitpeminjam,g.namalengkap 
                from ".self::table()." p 
                left join aset.ms_unit u on u.idunit = p.idunitpeminjam 
                left join sdm.v_biodatapegawai g on g.idpegawai = p.idpeminjam";
			
			return $sql;
		}

		function dataQuery($key){
		    $sql = "select p.*,g.namalengkap as peminjam 
		        from ".static::table()." p 
		        left join sdm.v_biodatapegawai g on g.idpegawai = p.idpeminjam 
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
		    return $conn->GetRow("select isok1,isverify,idunitasal,tglpinjam,tglkembali,tglpengajuan from ".self::table()." where idpinjam = '$key'");
		}
		
		function setPinjam($conn, $key, $old, $new){
		    $ok = true;
            if(isset($new) and $old != $new){
                if(empty($new) or $new == 'null') $ok = self::setSeriKembali($conn, $key);
                else $ok = self::setSeriPinjam($conn, $key);
            }
            return $ok;
		}
		
		function setKembali($conn, $key, $old, $new){
		    $ok = true;
            if(isset($new) and $old != $new){
                if(empty($new) or $new == 'null') $ok = self::setSeriPinjam($conn, $key);
                else $ok = self::setSeriKembali($conn, $key);
            }
            return $ok;
		}
		
		function setSeriPinjam($conn, $key){
	        $sql = "update aset.as_seri set idstatus = 'P' 
	            where idseri in (select idseri from aset.as_pinjamdetail where idpinjam = '$key')";
            return $conn->Execute($sql);
		}

		function setSeriKembali($conn, $key){
		    $sql = "update aset.as_seri set idstatus = 'A' 
		        where idseri in (select idseri from aset.as_pinjamdetail where idpinjam = '$key')";
	        return $conn->Execute($sql);
		}
		
		function mailPinjam($conn, $key) {
			global $conf;

		    require_once(Route::getModelPath('pinjamdetail'));
		    $p_modeldet = mPinjamDetail;
		    
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
			    $isi.='	 <td colspan="2">Sehubungan dengan adanya pengajuan peminjaman aset pada tanggal <b>'.Cstr::formatDateInd($a_mdata['tglpengajuan']).'</b> dengan rincian aset sebagai berikut:</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2">&nbsp;</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td colspan="2" class="highlights"><b>PEMINJAM</b></td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Unit</td>';
				$isi.='  <td>: '.$a_data['unitpeminjam'].'</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Nama</td>';
				$isi.='  <td>: '.$a_data['namapeminjam'].'</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td colspan="2" class="highlights"><b>PEMBERI PINJAMAN</b></td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Unit</td>';
				$isi.='  <td>: '.$a_data['unitasal'].'</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td>Nama</td>';
				$isi.='  <td>: '.$a_data['pemilik'].'</td>';
			    $isi.=' </tr>';
				$isi.=' <tr>';
			    $isi.='	 <td colspan="2">&nbsp;</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2"> ';
			    $isi.='     <table border="1" style="border-collapse:collapse;" width="900">';
			    $isi.='         <tr>';
			    $isi.='             <td align="center" width="30">No.</td>';
				$isi.='             <td align="center">Barang</td>';
			    $isi.='             <td align="center" width="100">Spesifikasi</td>';
			    $isi.='             <td align="center" width="115">Tgl. Perolehan</td>';
			    $isi.='             <td align="center" width="115">Tgl. Pinjam</td>';
				$isi.='             <td align="center" width="115">Tgl. Kembali</td>';
				$isi.='             <td align="center" width="200">Keterangan</td>';
			    $isi.='         </tr>';
				
				    if(count($det)>0){
				        $i = 0;
					    foreach($det as $id => $val){
					        $i++;
						    $isi.=' <tr>';
						    $isi.='  <td valign="top">'.$i.'</td>';
						    $isi.='  <td valign="top">'.$val['namabarang'].'</td>';
						    $isi.='  <td valign="top">'.$val['spesifikasi'].'</td>';
						    $isi.='  <td valign="top" align="center">'.CStr::formatDateInd($val['tglperolehan']).'</td>';
						    $isi.='  <td valign="top" align="center">'.CStr::formatDateInd($val['tglpinjam']).'</td>';
							$isi.='  <td valign="top" align="center">'.CStr::formatDateInd($val['tglkembali']).'</td>';
							$isi.='  <td valign="top">'.$val['catatan'].'</td>';
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
			    $isi.='	 <td colspan="2"><b>Dimohon tindak lanjut dari pengajuan perawatan tersebut untuk diproses di sistem SIM Aset sebagaimana mestinya. Terima Kasih</b></td>';
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
			    
			    $subject = 'Pengajuan Peminjaman Aset tgl. '.Cstr::formatDateInd($a_mdata['tglpengajuan']);
			
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
