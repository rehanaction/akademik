<?php
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
	require_once($conf['helpers_dir'].'pay.ueu.class.php');
	
	// koneksi database
	$conn = Query::connect();
	
	// mengambil setting global
	Akademik::setGlobal($conn);
	
	// set debug
	//if($_SERVER['REMOTE_ADDR'] == '192.168.1.33'  or $_SERVER['REMOTE_ADDR'] == '192.168.1.181x' or $_SERVER['REMOTE_ADDR'] == '192.168.1.21' or $_SERVER['REMOTE_ADDR'] == '192.168.1.146' or $_SERVER['REMOTE_ADDR'] == '192.168.1.117' or $_SERVER['REMOTE_ADDR'] == '192.168.1.28')
	//	$conn->debug = true;
	
	// cek flash
	$a_flash = Route::getFlashData();
	if(!empty($a_flash)) {
		foreach($a_flash as $k => $v)
			eval('$'."$k = '$v';");
	}
?>
