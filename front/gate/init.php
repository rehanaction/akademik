<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once('config.php');
	
	// session dan output buffer
	session_start();
	ob_start();
	
	// gunakan helper
	require_once($conf['helpers_dir'].'ldap.php');
	require_once($conf['helpers_dir'].'cstr.class.php');
	require_once($conf['helpers_dir'].'modul.class.php');
	require_once($conf['helpers_dir'].'query.class.php');
	require_once($conf['helpers_dir'].'route.class.php');
	require_once($conf['helpers_dir'].'ui.class.php');
	
	// koneksi database
	$conn = Query::connect();
	$conn->debug = false;
?>
