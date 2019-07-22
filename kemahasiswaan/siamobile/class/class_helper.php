<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	/**
	 * Class untuk helper aplikasi mobile
	 * @author Sevima
	 * @version 1.0
	 */
	class cHelper {
		/**
		* Mendapatkan token user setelah login
		* @return string token
		*/
		function getNewToken() {
			return bin2hex(openssl_random_pseudo_bytes(32));
		}
		
		/**
		* Mengubah kumpulan object menjadi JSON untuk return
		* @param object $array,... OPTIONAL unlimited
		* @return string json
		*/
		function getJSON($sys) {
			$array = array();
			
			// proses system
			$array[$sys->getIndexName()] = $sys->getAttributes();
			
			// lalu proses data
			$n = func_num_args();
			if($n > 1 and !$sys->isError()) {
				$array['data'] = array();
				
				for($i=1;$i<$n;$i++) {
					$obj = func_get_arg($i);
					if(!isset($obj))
						continue;
					
					// array index-data
					if(is_array($obj)) {
						$idx = $obj['index'];
						if(empty($idx))
							continue;
						
						if(!empty($obj['data'])) {
							$array['data'][$idx] = array();
							
							foreach($obj['data'] as $eobj) {
								if(is_array($eobj)) {
									$tobj = array();
									foreach($eobj as $sobj) {
										$attr = $sobj->getAttributes();
										$tobj[$sobj->getIndexName()] = $attr;
									}
								}
								else
									$tobj = $eobj->getAttributes();
								
								$array['data'][$idx][] = $tobj;
							}
						}
						else
							$array['data'][$idx] = null;
					}
					else {
						$attr = $obj->getAttributes();
						$array['data'][$obj->getIndexName()] = $attr;
					}
				}
			}
			if(empty($array['data']))
				$array['data'] = null;
			
			// $json = json_encode($array,JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
			$json = json_encode($array); // nggak pakai JSON_NUMERIC_CHECK karena string '01' dianggap 1 nantinya, JSON_UNESCAPED_SLASHES belum disupport
			
			// beberapa pengecekan
			$json = str_replace('\/','/',$json);
			// $json = preg_replace('/"(\w+)":/','$1:',$json);
			
			return $json;
		}
		
		/**
		* Kirim email untuk reset password
		* @param string $email
		* @param string $username
		* @param string $token
		* @return array
		*/
		function sendEmailReset($email,$username,$token) {
			global $conf;
			
			require_once($conf['includes_dir'].'phpmailer/class.phpmailer.php');
			
			$mail = new PHPMailer();
			$mail->IsSMTP();
			
			try {
				$mail->SMTPAuth		= true;
				$mail->SMTPSecure	= 'ssl';
				// $mail->SMTPDebug	= 2;
				$mail->Host			= 'smtp.googlemail.com';
				$mail->Port			= 465;
				$mail->Username		= 'sevima.dummy@gmail.com';
				$mail->Password		= 's3mbarang123';
				
				$mail->SetFrom($mail->Username,$conf['site']);
				$mail->AddAddress($email);
				
				$mail->Subject		= 'Bantuan Sandi '.$conf['site'];
				
				$body = 'Anda mendapatkan email ini karena anda ingin melakukan penyetelan ulang sandi '.$conf['site'].' atas pengguna "'.$username.'".';
				$body .= ' Untuk melanjutkan proses silahkan klik tautan berikut:'.PHP_EOL.PHP_EOL;
				$body .= $conf['gate'].'reset.php?t='.base64_encode($token);
				$body .= PHP_EOL.PHP_EOL.'Jika tautan tidak berfungsi, harap akses alamat di atas menggunakan peramban web.'.PHP_EOL.PHP_EOL;
				$body .= 'Jika anda tidak merasa meminta penyetelan ulang sandi, mungkin pengguna lain salah memasukkan email anda.';
				$body .= ' Anda tidak perlu melakukan tindakan di atas dan bisa mengabaikan email ini dengan aman.'.PHP_EOL.PHP_EOL;
				$body .= 'Hormat kami,'.PHP_EOL.'Tim '.$conf['site'];
				
				$mail->Body			= $body;
				
				$ok = $mail->Send();
				
				return $ok;
			} catch (phpmailerException $e) {
				return false;
			} catch (Exception $e) {
				return false;
			}
		}

		public static function uploadImage($field, $id, $path, $maxwidth = 720, $maxsize = 7000000){
			$filename = $_FILES[$field]['name'];
			if($_FILES[$field]['size'] > $maxsize){
				return 'Ukuran gambar terlalu besar (maksimal '.round(($maxsize / 1000000),2).'MB)';
			}

			$extention = end(explode('.', $filename));
			if(!in_array(strtolower($extention), array('png','jpg','jpeg','gif'))){
				return 'Format gambar upload harus gambar.';
			}else{
				$server = $_SERVER['SCRIPT_FILENAME'];
				$servervalid = str_replace("index.php", "", $server);

				$uploadname = md5($id) . md5($id . $filename).'-'.$filename;
				$uploadpath = $servervalid . $path .  '/' . $uploadname;
				$upload = move_uploaded_file($_FILES[$field]["tmp_name"], $uploadpath);
				if($upload){
					list($width, $height, $type) = getimagesize($uploadpath);
					$newwidth = $maxwidth;
					$newheight = round($maxwidth / $width * $height);

					$thumb = imagecreatetruecolor($newwidth, $newheight);
					switch ($type) {
				    	case IMAGETYPE_GIF:
				            $source = imagecreatefromgif($uploadpath);
				            break;
				        case IMAGETYPE_JPEG:
				            $source = imagecreatefromjpeg($uploadpath);
				            break;
				        case IMAGETYPE_PNG:
				            $source = imagecreatefrompng($uploadpath);
				            break;
				    }

					$resize = imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

					$upload = imagejpeg($thumb, $uploadpath, 70);
					if($resize){
						return TRUE;
					}
				}
			}
			return 'Upload gambar gagal';
		}

		public static function generateImage($id, $filename, $path){
			if(empty($filename)){
				return NULL;
			} else{
				global $conf;
				return $conf['root'].$conf['url'] .  $path .'/'. md5($id) . md5($id . $filename).'-'.$filename;
			}
		}

		public static function uploadFile($field, $id, $path, $maxsize = 7000000){
			$filename = $_FILES[$field]['name'];
			if($_FILES[$field]['size'] > $maxsize){
				return 'Ukuran gambar terlalu besar (maksimal '.round(($maxsize / 1000000),2).'MB)';
			}
			$server = $_SERVER['SCRIPT_FILENAME'];
			$servervalid = str_replace("index.php", "", $server);

			$uploadname = md5($id . $filename).'-'.md5($id) .'-'. $filename;
			$uploadpath = $servervalid  . $path .  '/' . $uploadname;
			$upload = move_uploaded_file($_FILES[$field]["tmp_name"], $uploadpath);
			if($upload){
				return TRUE;
			}
			return 'Upload file gagal';
		}

		public static function generateFile($id, $name, $path){
			if(empty($filename)){
				return NULL;
			} else{
				global $conf;
				return  $conf['root'].$conf['url'].$path .md5($id . $filename). '-' . md5($id) . '-' .$filename;
			}
		}
	}