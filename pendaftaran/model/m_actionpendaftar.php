<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('settingpendaftaran'));
	
	class mActionpendaftar extends mPendaftar {

		function insert($conn,$record,$r_periode='', $r_gel='', $r_jalur='',$r_sistemkuliah='',$r_token=''){
		
				$jml_error=0;
				
				$nopendaftar = mPendaftar::nopendaftar($conn,$r_periode, $r_gel, $r_jalur);
				$record['nopendaftar']  = $nopendaftar;
				$record['jalurpenerimaan'] = $r_jalur;
				$record['idgelombang'] = $r_gel;
				$record['periodedaftar'] = $r_periode;
				if (!empty ($r_token))
				$record['sistemkuliah'] = $r_sistemkuliah;
				
				//$record['raport_10_1'] = $_POST['raport_10_1'];
				//$record['raport_10_2'] = $_POST['raport_10_2'];
				//$record['raport_11_1'] = $_POST['raport_11_1'];
				//$record['raport_11_2'] = $_POST['raport_11_2'];
				//$record['raport_12_1'] = $_POST['raport_12_1'];
				
				$record['pilihanditerima'] = $record['pilihan1'];
				$record['t_updateuser'] = $nopendaftar;
				$record['isadministrasi'] = -1;
				$record['lulusujian'] = -1;
				
				//cek apakah transfer diisi, jika ya masuk non reg
				if($record['mhstransfer']==1)
					$record['isreg'] = -1;
				else
					$record['isreg'] = 0;
				if (!empty($_POST['xasalsmu']) and ($record['asalsmu']=='null' or $record['asalsmu'] == '' or empty ($record['asalsmu']))){
					list($p_posterr, $record['asalsmu']) = mPendaftar::insertSMU($conn,$_POST['xasalsmu'],$record['propinsismu'],$record['kodekotasmu']);
					if ($p_posterr)
						$ok = false;
				}
				
				$record['password'] = md5(str_replace('-','',$record['tgllahir']));
				$record['tgldaftar'] = date('Y-m-d');
				$record['tglregistrasi'] = date('Y-m-d');
				$record['tokenpendaftaran'] = $r_token;
				
				list($p_posterr,$p_postmsg) = self::insertRecord($conn,$record,true);
				
				if (!$p_posterr){
					unset($post);
					
					$p_postmsg.=' <br> <span style="font-size:13pt; font-weight:bold; color:blue">
							Nopendaftar anda adalah '.$nopendaftar.' Silahkan gunakan tanggal lahir untuk password, contoh 17-05-2014 maka password = 20140517
							<br>Silahkan datang ke bagian HUMAS STIE INABA untuk membayar tagihan pendaftaran</span>';
					$_SESSION['PENDAFTARAN']['FRONT']['USERID'] = $nopendaftar;
					$r_key = $nopendaftar;
					
					list($p_posterrtagihan,$p_postmsgtagihan,$jml) = self::generateTagihanKUA($conn,$record);

					$infopendaftaran = mSettingpendaftaran::getData($conn,1);
					$body = 'Informasi Pendaftaran. <br>Nomor pendaftar : '.$record['nopendaftar'].'<br>Nama '.$record['nama'].'<br> Pada : '.CStr::formatDateInd($record['tglregistrasi']).'<br>';
					$body.=$infopendaftaran['infoemailpendaftar'];
					
					$subject = 'Informasi Pendaftar STIE INABA';
					$tujuan = $record['email'];
					self::sendMail($tujuan,$subject,$body);					
				}else{
					$ok = false;
				}
				
				return array($p_posterr,$p_postmsg,$p_posterrtagihan,$p_postmsgtagihan,$r_key);
				
				}
		function update($conn,$record,$key, $r_periode, $r_gel, $r_jalur){

				$a_input = self::inputColumn($conn);
				
				list($p_posterr,$p_postmsg) = self::updateCRecord($conn,$a_input,$record,$key);
				
				return array ($p_posterr,$p_postmsg);
			}

		function generateTagihanKUA($conn,$record){
			
				$a_filter = array();
				$a_filter['sistemkuliah'] = $record['sistemkuliah'];
				$a_filter['jalurpenerimaan'] = $record['jalurpenerimaan'];
				$a_filter['gelombang'] = $record['idgelombang'];
				$a_filter['periode'] = $record['periodedaftar'];
				$a_filter['kodeunit'] = $record['pilihanditerima'];
				$a_filter['ispendaftar'] = true;
				$a_filter['nim'] = $record['nopendaftar'];
				$a_filter['aksescurl'] = true;
				$a_filter['act'] = 'generate';
				
				$url = 'http://siakad.inaba.ac.id/keuanganakademik/keuanganakademik/index.php?page=curl_gentagihanpendaftar';
				list($p_posterrtagihan,$p_postmsgtagihan,$jml) = self::getUrl($url,$a_filter);
			
			return array($p_posterrtagihan,$p_postmsgtagihan,$jml);
		}

		function sendMail($tujuan,$subject,$body){
			global $conf;
			include($conf['includes_dir'].'PHPMailer2/PHPMailerAutoload.php');
			
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				//$mail->SMTPDebug = 2;
				$mail->SMTPOptions = array(
				    'ssl' => array(
				        'verify_peer' => false,
				        'verify_peer_name' => false,
				        'allow_self_signed' => true
				    )
				);
				// Konfigurasi SMTP
				$mail->isSMTP();
				
				try {
					$mail->IsHTML();
					$mail->SMTPAuth = true;
					$mail->SMTPSecure = 'tls';
					$mail->Host     = $conf['smtp_host']; // SMTP servers
					$mail->SMTPAuth = true;
					$mail->Username = $conf['smtp_user'];
					$mail->Password = $conf['smtp_pass'];
					$mail->Port       = 587;//$conf['smtp_port'];
					$mail->ClearAddresses();
					$mail->AddAddress('humas.marketing@inaba.ac.id');
					$mail->From = $conf['smtp_email'];
					$mail->FromName ='IT-INABA';
					$mail->Subject = $subject;
					$mail->Body = $body;			
					$ok = $mail->Send();
					if ($ok){
						return array(false,'Pengiriman Email Berhasil');
					}
					else{
						return array(false,'Pengiriman Email Berhasil');
					}


					
				} catch (phpmailerException $e) {
					return array(true,'Pengiriman Email Gagal');
				} catch (Exception $e) {
					return array(true,'Pengiriman Email Gagal');
				}
			}
		}		
		
		
		
	}
	
	
?>
