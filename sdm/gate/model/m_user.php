<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['model_dir'].'m_model.php');
	
	class mUser extends mModel {
		const schema = 'gate';
		const table = 'sc_user';
		const order = 'userid';
		const key = 'userid';
		const label = 'user';
		const sequence = 'userid';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select distinct u.* from ".static::table()." u
					left join gate.sc_userrole ur on ur.userid = u.userid";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'role': return "ur.koderole = '$key'";
			}
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'role':
					$info['table'] = 'sc_userrole';
					$info['key'] = 'kodeunit,koderole,userid';
					$info['label'] = 'role user';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// role
		function getRole($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('sc_userrole')."
					where userid = '$key' order by koderole, kodeunit";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// ganti password
		function changePassword($conn,$userid) {
			$sql = "update ".self::table()." set password = gate.md5(coalesce(hints,'')),
					t_updateact = 'resetpass', ".Query::logUpdate()." where userid = '$userid'";
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		// tipe user
		function statusAktif() {
			return array('1' => 'Aktif', '0' => 'Tidak Aktif');
		}
		
		function useldap() {
			return array('1' => 'Ya');
		}
		
		// mendapatkan data hak akses user
		function getDataAuth($conn,$userid) {
			$sql = "select distinct ur.koderole, r.namarole, ur.kodeunit, u.namaunit, u.infoleft, u.inforight, mn.kodemodul, m.namamodul from gate.sc_userrole ur
					join gate.sc_role r on r.koderole = ur.koderole join gate.ms_unit u on u.kodeunit = ur.kodeunit
					left join (gate.sc_menurole mr join gate.sc_menu mn on mn.idmenu = mr.idmenu join gate.sc_modul m on m.kodemodul = mn.kodemodul) on mr.koderole = r.koderole
					where ur.userid = '$userid' and mn.kodemodul is not null order by m.namamodul, r.namarole, ur.kodeunit";
			
					$rs = $conn->Execute($sql);
			
			$a_data = array(); $t_modul = '';
			while($row = $rs->FetchRow()) {
				if($t_modul != $row['kodemodul']) {
					$t_data = array();
					$t_modul = $row['kodemodul'];
				}
				
				$t_data[] = $row;
				
				if($rs->EOF or $rs->fields['kodemodul'] != $t_modul)
					$a_data[$t_modul] = array('kode' => strtolower($row['kodemodul']), 'nama' => $row['namamodul'], 'data' => $t_data);
			}
			
			return $a_data;
		}
		
		// login
		function cekUserPass($conn,$userid,$passwd) {
			$sql = "select 1 from ".self::table()." where userid = '$userid' and password in ('$passwd')";
			$ispass = $conn->GetOne($sql);
			
			return (empty($ispass) ? false : true);
		}
		
		function logIn($conn,$userid,$passwd,$email=false) {
			$userid = CStr::removeSpecial($userid);
			
			if(!$email){
				//Pengecekan LDAP				
				$isldap = $conn->GetOne("select isuseldap from ".self::table()." where username = '$userid'");
				if($isldap == '1'){
					if($passwd !== false){
						list($ok, $msg) = self::ldappass($userid, $passwd);
						if($ok)
							$sql = "select * from ".self::table()." where username = '$userid'";
					}else
						$sql = "select * from ".self::table()." where username = '$userid'";
				}else{				
					if($passwd !== false) {
						if($passwd == '')
							$passwd = "coalesce(password,'') in ('','".md5($passwd)."')";
						else
							$passwd = "password = '".md5($passwd)."'";
					}
					
					$sql = "select * from ".self::table()." where username = '$userid'";
					if($passwd !== false)
						$sql .= " and $passwd";
				}
			}else{
				$sql = "select * from ".self::table()." where email = '".$userid."'";
			}
			
			// cek data user
			Modul::incLoginAttempt();
						
			$row = $conn->GetRow($sql);
			
			if(!empty($row)) {
				$conn->BeginTrans();
				
				// logout
				Modul::logOut(GATE_SITE_ID);
				
				$ok = true;
				
				// edit data login
				$now = date('Y-m-d H:i:s');
				$ip = $_SERVER['REMOTE_ADDR'];
				
				$userid = $row['username'];
				
				$sql = "update ".self::table()." set lastlogintime = '$now', lastloginip = '$ip' where username = '$userid'";
				$ok = $conn->Execute($sql);
				
				if($ok) {
					require_once(Route::getModelPath('usersession'));
					
					$record = array();
					$record['t_userid'] = $userid;
					$record['t_username'] = $row['userdesc'];
					$record['t_logintime'] = $now;
					$record['t_loginattempt'] = Modul::getLoginAttempt();
					$record['t_ipaddress'] = $ip;
					$record['t_hostname'] = $_SERVER['REMOTE_HOST'];
					$record['t_osname'] = Modul::getAgent();
					
					$err = mUserSession::insertRecord($conn,$record);
					$ok = Query::isOK($err);
					
					$row['loginsession'] = (string)mUserSession::getLastValue($conn);
				}
				
				$ok = $conn->CommitTrans($ok);
				
				if($ok)
					Modul::setSessionData($row);
			
				return $ok ? true : false;
			}
			else
				return false;
		}
		
		// logout
		function logOut($conn) {
			require_once(Route::getModelPath('usersession'));
			
			// update waktu logout
			$record = array();
			$record['t_logouttime'] = date('Y-m-d H:i:s');
			
			$err = mUserSession::updateRecord($conn,$record,Modul::getLoginSession());
			
			return $err ? false : true;
		}
		
		function ldappass($username, $password){
			$ok = false;
			$msg = '';
		    
			$app_user = 'uid=authenticate,ou=system,dc=ueu,dc=ac,dc=id';
			$app_pass = 'mysecret4system';
             
			//$username = 'suyudi';
			//$password = 'dragonfly';
             
			$userdn = '';
            
			error_reporting(0);			
			$conn_status = ldap_connect('ldap.esaunggul.ac.id', 389);
			if($conn_status === FALSE) {
				$ok = false;
				$msg = "Couldn't connect to LDAP service";
			}else{
				ldap_set_option($conn_status, LDAP_OPT_PROTOCOL_VERSION, 3);

				$bind_status = ldap_bind($conn_status, $app_user, $app_pass);
				if ($bind_status === FALSE) {
					$ok = false;
					$msg = "Couldn't bind to LDAP as application user";
				}else{
					$query = "(&(uid=" . $username . "))";
					$search_base = "dc=ueu,dc=ac,dc=id";
					$search_status = ldap_search(
						$conn_status, $search_base, $query, array('dn')
					);

					if($search_status === FALSE) {
						$ok = false;
						$msg = "Search on LDAP failed";
					}else{
						$result = ldap_get_entries($conn_status, $search_status);
						if ($result === FALSE) {
							$ok = false;
							$msg = "Couldn't pull search results from LDAP";
						}else{
							if((int) @$result['count'] > 0)
								$userdn = $result[0]['dn'];
                             
							if(trim((string) $userdn) == '') {
								$ok = false;
								$msg = "Empty DN. Something is wrong.";
							}else{
								$auth_status = ldap_bind($conn_status, $userdn, $password);
								if($auth_status === FALSE) {
									$ok = false;
									$msg = "Couldn't bind to LDAP as user!";
								}else{
									$ok = true;
								}
							}
						}
					}
				}
			}

			return array($ok, $msg);
		}
	}
?>
