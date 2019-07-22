<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['includes_dir'].'phpmailer/class.phpmailer.php');
	require_once(Route::getModelPath('cuti'));
	require_once(Route::getModelPath('model'));
	
	class mEmail extends mModel {
		const schema = 'sdm';
		
		// email dari atasan ke pegawai
		function confirmCuti($conn, $r_key) {
			global $conf;
			//mendapatkan data mail
			$sql = "select c.*, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama,p.nik,
					u.namaunit, p.email, m.jeniscuti
					from ".self::table('pe_rwtcuti')." c 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=c.idpegawai 
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					left join ".self::table('ms_cuti')." m on m.idjeniscuti = c.idjeniscuti
					where c.nourutcuti=$r_key";
			$data = $conn->GetRow($sql);
			
			//namaatasan
			$atas = $conn->GetRow("select sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					st.jabatanstruktural
					from ".self::table('pe_rwtstruktural')." s
					left join ".self::table('ms_pegawai')." p on s.idpegawai = p.idpegawai
					left join ".self::table('ms_struktural')." st on st.idjstruktural = s.idjstruktural
					where s.idpegawai = '".$data['nippejabat']."' and s.isvalid = 'Y' and s.tmtmulai <= now()::date and COALESCE(s.tmtselesai,now()::date) >= now()::date
					order by coalesce(s.isutama, 'T') desc, s.tmtmulai desc limit 1");
			
			//select dari pe_rwtcutidetail
			$a_cutidet = mCuti::getCutiDetail($conn, $r_key);
			
			//status cuti
			$a_status = mCuti::statusSetujuiCuti();
			$msg = $a_status[$data['statususulan']];
			$color = array('S' => 'green','T' => 'red');
			
			//cuti yang sudah diambil untuk jenis cuti dan tahun bersangkutan
			$ambilcuti = mCuti::getAmbilCuti($conn,$data['idpegawai'],$data['idjeniscuti'],$data['tglpengajuan']);
			$sisacuti = mCuti::getSisaCuti($conn,$data['idpegawai'],$data['idjeniscuti'],$data['tglpengajuan']);

			$p_tbwidth = 1000;
			
			$isi ='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			$isi.=' <tr>';
			$isi.='  <td></td>';
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
			$isi.='	 <td colspan="2">Dengan Hormat,</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td colspan="2">Permohonan cuti Bapak/ Ibu</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td colspan="2">&nbsp;</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td width="250">Tanggal Permohonan</td>';
			$isi.='	 <td>: '.Cstr::formatDate($data['tglpengajuan']).'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Tahun</td>';
			$isi.='	 <td>: '.$data['tahun'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>NPP</td>';
			$isi.='	 <td>: '.$data['nik'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Nama Lengkap</td>';
			$isi.='	 <td>: '.$data['nama'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Unit Kerja</td>';
			$isi.='	 <td>: '.$data['namaunit'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Jenis Cuti</td>';
			$isi.='	 <td>: '.$data['jeniscuti'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td valign="top">Tanggal</td>';
			$isi.='	 <td>: ';
				$isi.='<table border="1" style="border-collapse:collapse;" width="300">';
				$isi.=' <tr>';
				$isi.='  <td align="center">Tanggal Mulai</td>';
				$isi.='  <td align="center">Tanggal Selesai</td>';
				$isi.='  <td align="center">Lama Cuti</td>';
				$isi.=' </tr>';
				
				if(count($a_cutidet)>0){
					foreach($a_cutidet as $rowd){
						$isi.=' <tr>';
						$isi.='  <td align="center">'.Cstr::formatDate($rowd['tglmulai']).'</td>';
						$isi.='  <td align="center">'.Cstr::formatDate($rowd['tglselesai']).'</td>';
						$isi.='  <td align="center">'.$rowd['lamacuti'].' hari</td>';
						$isi.=' </tr>';
					}
				}
				
				$isi.='</table>';
			$isi.='  </td>';
			$isi.=' </tr>';	
			$isi.=' <tr>';
			$isi.='	 <td>Lama Cuti</td>';
			$isi.='	 <td>: '.$data['lamacuti'].' hari</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Untuk Keperluan</td>';
			$isi.='	 <td>: '.$data['alasancuti'].' hari</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Keterangan Cuti</td>';
			$isi.='	 <td>: Anda sudah mengambil cuti '.$ambilcuti.' hari '.($sisacuti != '' ? ', sisa '.$sisacuti.' hari ' : '').'untuk periode ini</td>';
			$isi.=' </tr>';
			$isi.='</table><br>';

			$isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			$isi.='	<tr>';
			$isi.='	 <td>Telah <font color="'.$color[$data['statususulan']].'"><b>'.strtolower($msg).'</b></font> pelaksanaannya pada tanggal '.Cstr::formatDate($data['tgldisetujui']).'</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>'.$atas['jabatanstruktural'].'</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>&nbsp;</td>';
			$isi.=' </tr>';
			$isi.='	<tr>';
			$isi.='	 <td>'.$atas['namalengkap'].'</td>';
			$isi.=' </tr>';
			$isi.='</table><br>';
			
			$tujuan = $data['email'];//dikirimkan ke email pegawai
			$subject = 'Konfirmasi Pengajuan Cuti '.$data['jeniscuti'].' Oleh : '.$data['nik'].' - '.$data['nama'];
			
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				$mail->IsSMTP();  // send via SMTP
				try {
					$mail->IsHTML(true);
					$mail->SMTPAuth = true;
					$mail->Host     = $conf['smtp_host']; // SMTP servers
					$mail->Username = $conf['smtp_user'];
					$mail->Password = $conf['smtp_pass'];
			        $mail->SMTPSecure = 'tls';
			        $mail->Port       = 587;
					$mail->ClearAddresses();
					$mail->AddAddress($tujuan);
					$mail->From = $conf['smtp_email'];
					$mail->FromName = $conf['smtp_admin'];
					$mail->Subject = $subject;
					$mail->Body = $isi;			
					$mail->Send();
					
					$err = false;
				} catch (phpmailerException $e) {
					$err = true;
				} catch (Exception $e) {
					$err = true;
				}
			}else
				$err = 'noaddress';
			
			if(!$err)
				$msg = 'Pengiriman email berhasil';
			else{
				if($err != 'noaddress')
					$msg = 'Alamat email tujuan kosong';
				else
					$msg = 'Pengiriman email gagal';
			}
			
			return array($err,$msg);
		}
		
		//dikirimkan ke atasan/ pimpinan unit
		function requestCuti($conn, $r_key) {
			global $conf;
			//mendapatkan data mail
			$sql = "select c.*, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama,p.nik,
					s.namaunit, p.email,p.emailatasan, m.jeniscuti
					from ".self::table('pe_rwtcuti')." c 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=c.idpegawai 
					left join ".self::table('ms_unit')." s on s.idunit=p.idunit
					left join ".self::table('ms_cuti')." m on m.idjeniscuti = c.idjeniscuti
					where c.nourutcuti=$r_key";
			$data = $conn->GetRow($sql);
			
			//select dari pe_rwtcutidetail
			$a_cutidet = mCuti::getCutiDetail($conn, $r_key);
				
			//cuti yang sudah diambil dengan jenis cuti dan tahun yang bersangkutan
			$ambilcuti = mCuti::getAmbilCuti($conn,$data['idpegawai'],$data['idjeniscuti'],$data['tglpengajuan']);
			$sisacuti = mCuti::getSisaCuti($conn,$data['idpegawai'],$data['idjeniscuti'],$data['tglpengajuan']);
			
			$p_tbwidth = 1000;
			
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
			$isi.='	 <td colspan="2">Mohon keputusan atas pengajuan cuti pegawai/ pejabat di jajaran unit Bapak/ Ibu :</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td colspan="2">&nbsp;</td>';
			$isi.=' <tr>';
			$isi.=' <tr>';
			$isi.='	 <td width="250">Tanggal Permohonan</td>';
			$isi.='	 <td>: '.Cstr::formatDate($data['tglpengajuan']).'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Tahun</td>';
			$isi.='	 <td>: '.$data['tahun'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>NPP</td>';
			$isi.='	 <td>: '.$data['nik'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Nama Lengkap</td>';
			$isi.='	 <td>: '.$data['nama'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Unit Kerja</td>';
			$isi.='	 <td>: '.$data['namaunit'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Jenis Cuti</td>';
			$isi.='	 <td>: '.$data['jeniscuti'].'</td>';
			$isi.=' </tr>';	
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td valign="top">Tanggal</td>';
			$isi.='	 <td>: ';
				$isi.='<table border="1" style="border-collapse:collapse;" width="300">';
				$isi.=' <tr>';
				$isi.='  <td align="center">Tanggal Mulai</td>';
				$isi.='  <td align="center">Tanggal Selesai</td>';
				$isi.='  <td align="center">Lama Cuti</td>';
				$isi.=' </tr>';
				
				if(count($a_cutidet)>0){
					foreach($a_cutidet as $rowd){
						$isi.=' <tr>';
						$isi.='  <td align="center">'.Cstr::formatDate($rowd['tglmulai']).'</td>';
						$isi.='  <td align="center">'.Cstr::formatDate($rowd['tglselesai']).'</td>';
						$isi.='  <td align="center">'.$rowd['lamacuti'].' hari</td>';
						$isi.=' </tr>';
					}
				}
				
				$isi.='</table>';
			$isi.='  </td>';
			$isi.=' </tr>';	
			$isi.=' <tr>';
			$isi.='	 <td>Lama Cuti</td>';
			$isi.='	 <td>: '.$data['lamacuti'].' hari</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Untuk Keperluan</td>';
			$isi.='	 <td>: '.$data['alasancuti'].'</td>';
			$isi.=' </tr>';
			$isi.=' <tr>';
			$isi.='	 <td>Keterangan Cuti</td>';
			$isi.='	 <td>: Anda sudah mengambil cuti '.$ambilcuti.' hari '.($sisacuti != '' ? ', sisa '.$sisacuti.' hari ' : '').'untuk periode ini</td>';
			$isi.=' </tr>';
			$isi.='</table><br>';
			
			$isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			$isi.='	<tr>';
			$isi.='	 <td>';
			$isi.='	 Silahkan melakukan melalui web : <a href="http://sim.unusa.ac.id">sim.unusa.ac.id</a>';
			$isi.='	 </td>';
			$isi.='</tr>';
			$isi.='</table>';
			
			$tujuan = $data['emailatasan'];//email atasan
			$subject = 'Pengajuan Cuti '.$data['jeniscuti'].' atas nama '.$data['nama'];
			
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				$mail->IsSMTP();  // send via SMTP
				try {
					$mail->IsHTML(true);
					$mail->SMTPAuth = true;
					$mail->Host     = $conf['smtp_host']; // SMTP servers
					$mail->Username = $conf['smtp_user'];
					$mail->Password = $conf['smtp_pass'];
			        $mail->SMTPSecure = 'tls';
			        $mail->Port       = 587;
					$mail->ClearAddresses();
					$mail->AddAddress($tujuan);
					$mail->From = $conf['smtp_email'];
					$mail->FromName = $conf['smtp_admin'];
					$mail->Subject = $subject;
					$mail->Body = $isi;			
					$mail->Send();
					
					$err = false;
				} catch (phpmailerException $e) {
					$err = true;
				} catch (Exception $e) {
					$err = true;
				}
			}else
				$err = 'noaddress';
			
			if(!$err)
				$msg = 'Pengiriman email berhasil';
			else{
				if($err != 'noaddress')
					$msg = 'Alamat email tujuan kosong';
				else
					$msg = 'Pengiriman email gagal';
			}
			
			return array($err,$msg);
		}
		
		function tesMail(){
			global $conf;
			$tujuan = 'wijaya.echo@yahoo.com';	
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				$mail->IsSMTP();    // send via SMTP
				try {
					$mail->IsHTML(true);
					//$mail->SMTPAuth = true;
					$mail->Host     = $conf['smtp_host']; // SMTP servers
					$mail->Username = $conf['smtp_user'];
					$mail->Password = $conf['smtp_pass'];
					$mail->ClearAddresses();
					$mail->AddAddress($tujuan);
					$mail->Subject = 'tes Email';
					$mail->Body = 'asdfasdfasdf';			
					if($mail->Send()) {
					  echo "Message sent!";
					} else {
					  echo "Mailer Error: " . $mail->ErrorInfo;
					}
						
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
