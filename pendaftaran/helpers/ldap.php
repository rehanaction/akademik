<?php
	// fungsi direct redirect
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	class Ldap {

		public function userAdd( $arg=array(), &$err=array() )
		{
			$groups = array( "M"=>"ou=students", "D"=>"ou=lecturers", "K"=>"ou=employees" );
			$passwd = $arg["passwd"];
			$entry = array(
					"objectClass"	=> array("inetOrgPerson","organizationalPerson","person","top"),
					"cn"			=> $arg["cn"], 
					"sn"			=> $arg["sn"],
					// "uid"			=> "",
					"mail"		    => $arg["mail"],
					// "mobile"        => "",
					// "ou"			=> "",
					// "userPassword"	=> "{SHA}" . base64_encode( pack( "H*", sha1( $password ) ) )
					"userPassword"	=> $this->userPasswordCreate($passwd)
				);
			
			$rdn=$groups[$arg["userType"]].",".$this->_rootDN;
			$rdn="uid=".$arg["uid"].",".$rdn;
			
			$ldap = ldap_add ( $this->_conn, $rdn , $entry );
			if ( $ldap )
			{
				return true;
			}
			else
			{
				$err["code"]=ldap_errno ( $this->_conn );
				$err["mesg"]=ldap_error ( $this->_conn );
				return false;
        	}
		}

		public function userGetEntry($uid, &$err=array())
		{

			if ( !$this->_conn ) return false;

			$filter="(uid=".$uid.")";
			$result=ldap_search($this->_conn, $this->_rootDN, $filter);
			if (!$result)
			{
				$err["code"]="";
				$err["mesg"]="username not found";
				return false;
			}
			// jika hasil pencarian menghasilkan > 1 record/entry, return false
			$entry = ldap_get_entries($this->_conn, $result);
			if ( $entry["count"]>1 ){	
				$err["code"]="";
				$err["mesg"]="Terdapat lebih dari 1 username";
				return false;			
			}
			if ( $entry["count"]==0 ){	
				$err["code"]="";
				$err["mesg"]="username not found";
				return false;			
			}
			$entry = ldap_first_entry($this->_conn, $result);
			return $entry;

		}
		
		public function userPasswordisMatch($uid, $passwd="", &$err=array())
		{
			$entry = $this->userGetEntry($uid,$err);
			if(!$entry) return false;
			$attr=ldap_get_attributes($this->_conn,$entry);
			$existPassword=$attr["userPassword"][0];
			$match=false;
			if ( strpos($existPassword,"{MD5}")!==false ){
				$passwd = "{MD5}".base64_encode( pack( "H*", md5( $passwd ) ) );
			}
			if ( strpos($existPassword,"{SHA}")!==false ){
				$passwd = "{SHA}".base64_encode( pack( "H*", sha1( $passwd ) ) );
			}
			if ( strpos($existPassword,"{SSHA}")!==false ){
				$salt	= substr(base64_decode(substr($existPassword,6)),20);
				$passwd = "{SSHA}".base64_encode( sha1( $passwd.$salt,true ).$salt );
			}
			$match = $existPassword==$passwd ? true:false;
			return $match;
		}
		
		public function userPasswordCreate($passwd="")
		{
			//return "{SHA}" . base64_encode( pack( "H*", sha1( $passwd ) ) );
			$salt=substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!@#$%^&*+_(){}[]',4)),0,10);
			return "{SSHA}".base64_encode( sha1( $passwd.$salt,true ).$salt );
		}
		
		public function userUpdatePassword($uid, $passwd="", &$err=array())
		{

			$entry = $this->userGetEntry($uid,$err);
			if(!$entry) return false;
			$rdn=ldap_get_dn ( $this->_conn, $entry );
			if (!$rdn) return false;

			$attr = array(
					"userPassword"	=> $this->userPasswordCreate($passwd)
				);
				
			if ( !ldap_mod_replace ( $this->_conn, $rdn , $attr ) )
			{
				$err["code"]=ldap_errno ( $this->_conn );
				$err["mesg"]=ldap_error ( $this->_conn );			
				return false;
			}
			return true;
			
		}

		public function userUpdate($uid, $attr=array(), &$err=array())
		{

			$entry = $this->userGetEntry($uid,$err);
			if(!$entry) return false;
			$rdn=ldap_get_dn ( $this->_conn, $entry );
			if (!$rdn) return false;

			if ( empty($attr) )
			{
				$err["code"]="";
				$err["mesg"]="Attribut is empty";
				return false;
			}
			if ( !ldap_mod_replace ( $this->_conn, $rdn , $attr ) )
			{
				$err["code"]=ldap_errno ( $this->_conn );
				$err["mesg"]=ldap_error ( $this->_conn );			
				return false;
			}
			return true;
		}

		public function userLogin($uid, $password, &$err=array())
		{

			$entry = $this->userGetEntry($uid,$err);
			if(!$entry) return false;
			$rdn=ldap_get_dn ( $this->_conn, $entry );
			
			if (!$rdn) return false;
			
			if ($password===false) return true;
			
			ldap_set_option($this->_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			$bind=ldap_bind($this->_conn, $rdn, $password );
			if ($bind)
			{
 				return $bind;
			}
 			$err["code"]=ldap_errno ( $this->_conn );
			$err["mesg"]=ldap_error ( $this->_conn );
			return false;
			
		}
		
		public function isLdapConnected()
		{
			return $this->_conn ? true : false;
		}
		
		protected $_conn=false;
		protected $_rootDN="";
		protected function _connect()
		{
			global $conf;
			$this->_conn=ldap_connect($conf['ldap_server']);
			// $this->_conn=ldap_connect($server);
			if ( $this->_conn )
			{
				ldap_set_option($this->_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
				$bind=ldap_bind($this->_conn, $conf['ldap_user'], $conf['ldap_pass'] );
				if ($bind)
				{
					$this->_rootDN = $conf['ldap_base'];
					return true;
				}
			}
			return false;
		}

		function __construct()
		{
			$this->_connect();
		}
		
		function __destruct()
		{
			ldap_close($this->_conn);
		}
	}
?>
