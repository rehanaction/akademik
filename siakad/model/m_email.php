<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	global $conf;
	require_once($conf['includes_dir'].'phpmailer/class.phpmailer.php');
	
	class mEmail extends mModel {
		const schema = 'akademik';
		
		function sendMail($tujuan,$subject,$body){
			global $conf;
			
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				$mail->IsSMTP();    // send via SMTP
				try {
					$mail->IsHTML(true);
					$mail->SMTPAuth = true;
					//$mail->SMTPDebug	= 2;
					$mail->Host     = $conf['smtp_host']; // SMTP servers
					$mail->Username = $conf['smtp_user'];
					$mail->Password = $conf['smtp_pass'];
					$mail->SMTPSecure = "tls";
					$mail->Port       = 587;
					$mail->ClearAddresses();
					$mail->From = $conf['smtp_email'];
					$mail->FromName = $conf['smtp_admin'];
					$mail->AddAddress($tujuan);
					$mail->Subject = $subject;
					$mail->Body = $body;
					
					
					
					if($mail->Send()) {
						$err=false;
					  $msg="Message sent!";
					} else {
						$err=true;
					  $msg="Mailer Error: " . $mail->ErrorInfo;
					}
						
					return array($err,$msg);
				} catch (phpmailerException $e) {
					return array(true,$e);
				} catch (Exception $e) {
					return array(true,$e);
				}
			}
		}
	}
?>
