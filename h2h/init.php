<?php
	// konstanta
	// define('H2H_KODEFORMULIR',1);
	// define('H2H_JENISFORMULIR','FRM');
	define('H2H_KODEFORMULIR','01');
	define('H2H_JENISFORMULIR','01');
	define('H2H_CURRENCY','IDR');
	define('H2H_LOGKETLENGTH',400);

	define('BANK_BUKOPIN',441);

	// tanpa error
	error_reporting(0);
	
	// pakai session
	session_start();
	
	// cek sevima :D
	if($_SERVER['REMOTE_ADDR'] == '36.85.91.184' or $_SERVER['REMOTE_ADDR'] == '36.74.13.119')
		define('H2H_DEBUG',true);
	
	if(defined('H2H_DEBUG')) {
		ini_set('display_errors','On');
		error_reporting(E_ERROR);
	}
	
	// config
	require_once('config.php');
	
	// nusoap
	require_once('includes/nusoap/nusoap.php');
	
	// database
	require_once('includes/adodb5/adodb.inc.php');
	
	$conn = ADONewConnection($conf['db_driver']);
	$conn->Connect('host=' . $conf['db_host'] . ' port=' . $conf['db_port'] . ' dbname='  . $conf['db_dbname'] . ' user=' . $conf['db_username'] . ' password=' . $conf['db_password']);
	$conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if(defined('H2H_DEBUG'))
		$conn->debug = true;
	
	// include model
	require_once('model/m_h2h.php');
	require_once('model/m_tagihan.php');
	require_once('model/m_akademik.php');
	
	// include helpers
	require_once('helpers/helper.class.php');
?>