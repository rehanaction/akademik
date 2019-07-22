<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once('config.php');
	
	// session dan output buffer
	session_start();
	ob_start();
	
	// gunakan helper
	require_once($conf['helpers_dir'].'cstr.class.php');
	require_once($conf['helpers_dir'].'date.class.php');
	require_once($conf['helpers_dir'].'modul.class.php');
	require_once($conf['helpers_dir'].'page.class.php');
	require_once($conf['helpers_dir'].'query.class.php');
	require_once($conf['helpers_dir'].'route.class.php');
	require_once($conf['helpers_dir'].'ui.class.php');
	
	require_once($conf['helpers_dir'].'akademik.class.php');
	
	// koneksi database
	$conn = Query::connect();
	
//	if($_SERVER['REMOTE_ADDR'] == '192.168.1.33' || $_SERVER['REMOTE_ADDR'] == '192.168.1.158')
//		$conn->debug = true;
	
	// untuk updateact
	$i_page = 'siamobile';
?>
