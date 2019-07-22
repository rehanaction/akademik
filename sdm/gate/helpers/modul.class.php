<?php
	// fungsi pembantu modul
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class Modul {
		const encryptKey = 'SENTRAVIDYAUTAMA'; // key untuk dekripsi
		
		// ganti role
		function changeRole($role,$unit) {
			// cek dengan session
			$switch = false;
			foreach(self::getAccessRole() as $t_akses) {
				if($t_akses['role'] == $role and $t_akses['unit'] == $unit) {
					$switch = $t_akses;
					break;
				}
			}
			
			if($switch !== false) {
				$sess = $_SESSION[SITE_ID];
				
				// ubah beberapa data session
				$modul = $sess['MODUL'];
				$modul['ROLE'] = $switch['role'];
				$modul['ROLENAME'] = $switch['namarole'];
				$modul['UNIT'] = $switch['unit'];
				$modul['UNITNAME'] = $switch['namaunit'];
				$modul['LEFT'] = $switch['left'];
				$modul['RIGHT'] = $switch['right'];
				
				$sess['MODUL'] = $modul;
				
				// ambil lagi session menu
				$sess['MENU'] = self::getMenuRole($modul['CODE'],$modul['ROLE']);
				
				$_SESSION[SITE_ID] = $sess;
			}
		}
		
		// fungsi membuat menu
		function createMenu(&$i) {
			$arrmenu = self::getMenu();
			
			if(empty($arrmenu[$i]))
				return '';
			
			$menu = $arrmenu[$i];
			$level = $menu['levelmenu'];
			$nextlevel = $arrmenu[$i+1]['levelmenu'];
			
			if(empty($menu['namafile']))
				$href = 'javascript:void(0)';
			else
				$href = 'index.php?page='.$menu['namafile'];
			
			if($menu['namamenu'] == ':separator')
				$nama = '<hr>';
			else
				$nama = '<a href="'.$href.'">'.$menu['namamenu'].'</a>';
			
			$str = '<li>'.$nama;
			if($nextlevel > $level) {
				if($level == 0)
					$class = 'subnav';
				else
					$class = 'leafnav';
				
				$str .= "\n".'<ul class="'.$class.'">'."\n";
				$str .= self::createMenu(++$i);
				$str .= '</ul>'."\n";
			}
			else
				$str .= '</li>'."\n";
			
			$nextlevel = $arrmenu[$i+1]['levelmenu'];
			if($nextlevel < $level)
				return $str;
			else
				return $str.self::createMenu(++$i);
		}
				
		// dekripsi data session
		function decryptSessionData($data) {
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			
			$dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, self::encryptKey, $data, MCRYPT_MODE_ECB, $iv);
			
			return $dec;
		}
		
		// enkripsi data session
		function encryptSessionData($data) {
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$enc = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::encryptKey, $data, MCRYPT_MODE_ECB, $iv);
		
			return $enc;
		}
		
		// mendapatkan hak akses halaman
		function getFileAuth($file='',$ajax=false) {
			global $conf, $conn;
			
			// cek autentikasi juga
			if(!self::isAuthenticated()) {
				if($ajax) {
					echo	'<script type="text/javascript">
								location.href = "'.$conf['menu_path'].'";
							</script>';
					exit();
				}
				else
					Route::redirect($conf['menu_path']);
			}
			
			// jika tidak disebut, ambil file sekarang
			if(empty($file)) {
				$mustread = true;
				$file = Route::thisPage();
				$filesrc = Route::thisPage(true);
			}
			
			// cek dulu di menu
			$arrmenu = self::getMenu();
			foreach($arrmenu as $menu) {
				if($menu['namafile'] == $file or (strpos($menu['namafile'],'&') !== false and $menu['namafile'] == substr($filesrc,0,strlen($menu['namafile'])))) {
					$t_kodeakses = $menu['akses'];
					
					$akses['caninsert'] |= (empty($t_kodeakses[0]) ? false : true);
					$akses['canupdate'] |= (empty($t_kodeakses[1]) ? false : true);
					$akses['candelete'] |= (empty($t_kodeakses[2]) ? false : true);
					
					$t_kodeakses = substr($t_kodeakses,3);
					for($i=0;$i<strlen($t_kodeakses);$i++)
						$akses['canother'][$t_kodeakses[$i]] = true;
				}
			}
			
			// tidak ada, ambil di gate
			if(empty($akses)) {
				require_once($conf['gate_dir'].'model/m_role.php');
				
				$arrmenu = mRole::getFileAuth($conn,Modul::getModul(),$file,Modul::getRole());
				
				foreach($arrmenu as $menu) {
					$akses['caninsert'] |= (empty($menu['caninsert']) ? false : true);
					$akses['canupdate'] |= (empty($menu['canupdate']) ? false : true);
					$akses['candelete'] |= (empty($menu['candelete']) ? false : true);
					
					$t_kodeakses = $menu['aksesmenu'];
					for($i=0;$i<strlen($t_kodeakses);$i++)
						$akses['canother'][$t_kodeakses[$i]] = true;
				}
			}
			
			// cek akses
			if(empty($akses) and $mustread) {
				if($ajax) {
					echo	'<script type="text/javascript">
								location.href = "'.Route::navAddress('home').'";
							</script>';
					exit();
				}
				else
					Route::navigate('home');
			}
			
			return $akses;
		}
		
		// mendapatkan array menu
		function getMenuRole($modul,$role) {
			global $conf, $conn;
			
			// ambil dari model gate :D
			require_once($conf['gate_dir'].'model/m_menu.php');
			
			return mMenu::getArrMenu($conn,$modul,$role);
		}
		
		// mendapatkan data session (dari gate)
		function getSessionData($data) {
			global $conf;
			
			$xml = self::decryptSessionData($data);
			
			$p = xml_parser_create();
			xml_parse_into_struct($p,$xml,$data,$idx);
			xml_parser_free($p);
			
			$modul = array();
			$modul['USERID'] = $data[$idx['USERID'][0]]['value'];
			$modul['USERNAME'] = $data[$idx['USERNAME'][0]]['value'];
			$modul['USERDESC'] = $data[$idx['USERDESC'][0]]['value'];
			$modul['LASTLOGIN'] = $data[$idx['LASTLOGIN'][0]]['value'];
			$modul['ROLE'] = $data[$idx['KODEROLE'][0]]['value'];
			$modul['ROLENAME'] = $data[$idx['NAMAROLE'][0]]['value'];
			$modul['UNIT'] = $data[$idx['KODEUNIT'][0]]['value'];
			$modul['UNITNAME'] = $data[$idx['NAMAUNIT'][0]]['value'];
			$modul['LEFT'] = $data[$idx['INFOLEFT'][0]]['value'];
			$modul['RIGHT'] = $data[$idx['INFORIGHT'][0]]['value'];
			$modul['CODE'] = $data[$idx['KODEMODUL'][0]]['value'];
			$modul['NAME'] = $data[$idx['NAMAMODUL'][0]]['value'];
			
			$_SESSION[SITE_ID]['MODUL'] = $modul;
			
			// hak akses
			foreach($idx['ROLEAKSES'] as $t_idx => $t_idxdata) {
				$t_akses = array();
				$t_akses['role'] = $data[$t_idxdata]['value'];
				$t_akses['namarole'] = $data[$idx['NAMAROLEAKSES'][$t_idx]]['value'];
				$t_akses['unit'] = $data[$idx['UNITAKSES'][$t_idx]]['value'];
				$t_akses['namaunit'] = $data[$idx['NAMAUNITAKSES'][$t_idx]]['value'];
				$t_akses['left'] = $data[$idx['LEFTAKSES'][$t_idx]]['value'];
				$t_akses['right'] = $data[$idx['RIGHTAKSES'][$t_idx]]['value'];
				
				$akses[] = $t_akses;
			}
			
			$_SESSION[SITE_ID]['HAKAKSES'] = $akses;
			
			// menu
			$_SESSION[SITE_ID]['MENU'] = self::getMenuRole($modul['CODE'],$modul['ROLE']);
		}
		
		// mendapatkan nama browser
		function getAgent() {
			$useragent = $_SERVER['HTTP_USER_AGENT'];
			
			$fo = strpos($useragent,'(');
			$fc = strpos($useragent,')');
			
			$major = substr($useragent,0,$fo-1);
			$os = substr($useragent,$fo+1,$fc-$fo-1);
			$minor = substr($useragent,$fc+2,strlen($useragent)-$fc-2);
			
			$a_os = explode('; ',$os);
			
			$par = false;
			$a_minor = explode(' ',$minor);
			foreach($a_minor as $i => $t_minor) {
				if($t_minor[0] == '(')
					$par = $i-1;
				
				if($par !== false) {
					$a_minor[$par] .= ' '.$t_minor;
					unset($a_minor[$i]);
				}
				
				if($t_minor[strlen($t_minor)-1] == ')')
					$par = false;
			}
			
			if($a_os[0] == 'compatible') {
				$t_os = $a_os[2];
				$t_browser = $a_os[1];
			}
			else {
				$t_os = $a_os[0];
			
				list($t_web,$t_ver) = explode('/',$major);
				if($t_web == 'Opera')
					$t_browser = $major;
				else {
					end($a_minor);
					prev($a_minor);
					
					$t_current = current($a_minor);
					list($t_web,$t_ver) = explode('/',$t_current);
					if($t_web == 'Chrome')
						$t_browser = $t_current;
					else
						$t_browser = end($a_minor);
				}
			}
			
			return $t_os.' '.$t_browser;
		}
		
		// mendapatkan data session
		function getAccessRole() {
			return $_SESSION[SITE_ID]['HAKAKSES'];
		}
		
		function getLastLogin() {
			return $_SESSION[SITE_ID]['MODUL']['LASTLOGIN'];
		}
		
		function getLeftRight() {
			return array('LEFT' => $_SESSION[SITE_ID]['MODUL']['LEFT'], 'RIGHT' => $_SESSION[SITE_ID]['MODUL']['RIGHT']);
		}
		
		function getLoginAttempt() {
			return $_SESSION[SITE_ID]['LOGINATTEMPT'];
		}
		
		function getLoginSession() {
			return $_SESSION[SITE_ID]['MODUL']['LOGINSESSION'];
		}
		
		function getMenu() {
			return $_SESSION[SITE_ID]['MENU'];
		}
		
		function getModul() {
			return $_SESSION[SITE_ID]['MODUL']['CODE'];
		}
		
		function getRole() {
			return $_SESSION[SITE_ID]['MODUL']['ROLE'];
		}
		
		function getUnit() {
			return $_SESSION[SITE_ID]['MODUL']['UNIT'];
		}
		
		function getUserID() {
			return $_SESSION[SITE_ID]['MODUL']['USERID'];
		}
		
		function getUserName() {
			return $_SESSION[SITE_ID]['MODUL']['USERNAME'];
		}
		
		function getUserDesc() {
			return $_SESSION[SITE_ID]['MODUL']['USERDESC'];
		}
		
		// tambah login attempt
		function incLoginAttempt() {
			if(empty($_SESSION[SITE_ID]['LOGINATTEMPT']))
				$_SESSION[SITE_ID]['LOGINATTEMPT'] = 1;
			else
				$_SESSION[SITE_ID]['LOGINATTEMPT']++;
		}
		
		// cek otentikasi
		function isAuthenticated() {
			$userid = self::getUserID();
			if(empty($userid))
				return false;
			else
				return true;
		}
		
		// logout
		function logOut($siteid='') {
			if(empty($siteid))
				$siteid = SITE_ID;
			
			unset($_SESSION[$siteid]);
		}
		
		// refresh untuk pager
		function refreshList() {
			unset($_SESSION[SITE_ID]['EX'][Route::thisPage()]);
		}
		
		// menyimpan data session (dari user)
		function setSessionData($data) {
			if(defined('GATE_SITE_ID'))
				$siteid = GATE_SITE_ID;
			else
				$siteid = SITE_ID;
				
			$modul = array();
			$modul['USERID'] = $data['userid'];
			$modul['USERNAME'] = $data['username'];
			$modul['USERDESC'] = $data['userdesc'];
			$modul['LASTLOGIN'] = $data['lastlogintime'];
			$modul['LOGINSESSION'] = $data['loginsession'];
			$modul['USEREMAIL'] = $data['email'];
			$modul['IDPEGAWAI'] = $data['idpegawai'];
			
			$_SESSION[$siteid]['MODUL'] = $modul;
		}
		
		// menyimpan request, untuk combo
		function setRequest($req,$key='') {
			if(isset($req)) {
				$req = CStr::removeSpecial($req);
				if(!empty($key))
					$_SESSION[SITE_ID]['VAR'][$key] = $req;
				
				return $req;
			}
			else if(!empty($key))
				return self::getRequest($key);
		}
		
		function getRequest($key) {
			return $_SESSION[SITE_ID]['VAR'][$key];
		}
		
		// fungsi xml, seharusnya bukan methodnya modul sih :D
		function setDocChild($doc,$parentnode,$childkey,$text=false) {
			$childnode = $doc->createElement($childkey);
			$childnode = $parentnode->appendChild($childnode);
			
			if($text !== false) {
				
					$textnode = $doc->createTextNode($text);
					$textnode = $childnode->appendChild($textnode);
		
			}
			
			return $childnode;
		}
		
		// ambil data session sistem terpilih
		function setSIMSession($conn,$kodemodul,$koderole,$kodeunit) {
			global $conf;
			
			// include
			require_once($conf['model_dir'].'m_user.php');
			
			// cek dengan hak akses terlebih dahulu
			$isauth = false;
			$a_aksesmodul = array();
			$akses = mUser::getDataAuth($conn,self::getUserID());
			foreach($akses[$kodemodul]['data'] as $t_data) {
				if($t_data['koderole'] == $koderole and trim($t_data['kodeunit']) == $kodeunit) {
					$isauth = true;
					$t_namarole = $t_data['namarole'];
					$t_namaunit = $t_data['namaunit'];
					$t_namamodul = $t_data['namamodul'];
					$t_infoleft = $t_data['infoleft'];
					$t_inforight = $t_data['inforight'];
				}
				
				// sekalian daftar role modul
				$t_akses = array();
				$t_akses['role'] = $t_data['koderole'];
				$t_akses['namarole'] = $t_data['namarole'];
				$t_akses['unit'] = $t_data['kodeunit'];
				$t_akses['namaunit'] = $t_data['namaunit'];
				$t_akses['left'] = $t_data['infoleft'];
				$t_akses['right'] = $t_data['inforight'];
				
				$a_aksesmodul[] = $t_akses;
			}
			
			// tidak bisa akses
			if(!$isauth)
				return false;
			
			$doc = new DOMDocument();
			$doc->formatOutput = true;
			
			$root = self::setDocChild($doc,$doc,'session');
			
			// user
			$user = self::setDocChild($doc,$root,'user');
			
			foreach($_SESSION[SITE_ID]['MODUL'] as $key => $val)
				self::setDocChild($doc,$user,strtolower($key),$val);
			
			self::setDocChild($doc,$user,'koderole',$koderole);
			self::setDocChild($doc,$user,'namarole',$t_namarole);
			self::setDocChild($doc,$user,'kodeunit',$kodeunit);
			self::setDocChild($doc,$user,'namaunit',$t_namaunit);
			self::setDocChild($doc,$user,'infoleft',$t_infoleft);
			self::setDocChild($doc,$user,'inforight',$t_inforight);
			self::setDocChild($doc,$user,'kodemodul',$kodemodul);
			self::setDocChild($doc,$user,'namamodul',$t_namamodul);
			
			// hak akses
			$akses = self::setDocChild($doc,$root,'akses');
			
			$pref = 'akses';
			foreach($a_aksesmodul as $data) {
				$node = self::setDocChild($doc,$akses,'data'.$pref);
				
				self::setDocChild($doc,$node,'role'.$pref,$data['role']);
				self::setDocChild($doc,$node,'namarole'.$pref,$data['namarole']);
				self::setDocChild($doc,$node,'unit'.$pref,$data['unit']);
				self::setDocChild($doc,$node,'namaunit'.$pref,$data['namaunit']);
				self::setDocChild($doc,$node,'left'.$pref,$data['left']);
				self::setDocChild($doc,$node,'right'.$pref,$data['right']);
			}
			
			$xml = $doc->saveXML();
			
			return self::encryptSessionData($xml);
		}
		
		// log sesuatu
		function logText($text='') {
			$file = 'log.txt';
			
			if(is_array($text))
				$text = print_r($text,true);
			else if(empty($text))
				$text = ob_get_contents();
			
			global $i_page;
			$text = date('Y-m-d H:i:s').' '.$i_page."\r\n\r\n".$text;
			
			@unlink('log.txt');
			file_put_contents($file,$text);
		}
	}
?>
