<?php
ini_set('display_errors', 1);

define( 'SITE_ID', 'ESAUNGGULGATE' );
define( 'SITE_NAME', 'Esa Unggul' );

echo "ada";
print_r($_POST);
require_once('../model/m_user2.php');
require_once('../helpers/route2.class.php');
require_once('../helpers/query2.class.php');
require_once('../../siakad/includes/adodb5/adodb.inc.php');

$driver = 'postgres';
$host = 'localhost';
$port ='5432';
$user = 'siakad';
$password = 'keberkahan2020';
$db = 'siakad_fix';

$strconn = 'host=' . $host . ' dbname='  . $db . ' user=' . $user. ' password=' . $password;
$strconn .= ' port=' . $port;
$conn = ADONewConnection($driver);
$conn->Connect($strconn);
$conn->SetFetchMode(ADODB_FETCH_ASSOC);

if(!empty($_POST['txtUserID'])) {

    //$conn->debug=true;
		// mengambil data dari textbox
		$r_user = $_POST['txtUserID'];
		$r_password = $_POST['txtPassword'];
		//if($r_user == '201432022')
			//$conn->debug = true;

		print_r(mUser2::logIn($conn, $r_user,$r_password));

		//if($r_user == '201432022'){
			//ini_set('display_errors',true);
			//die('kena');
		//}
		//if($ok)
		//	Route2::navigate('menu');
		//else
		//	$alert = "Login gagal. Username atau Password anda salah.";
	}


?>
