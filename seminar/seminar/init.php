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
	
	// koneksi database
	$conn = Query::connect();
	//$conn_sdm = Query::connect('sdm');
	
	// mengambil setting global
	Akademik::setGlobal($conn);
	
	/*if($_SERVER['REMOTE_ADDR'] == '192.168.1.101' OR $_SERVER['REMOTE_ADDR'] == '192.168.1.21' 
	OR $_SERVER['REMOTE_ADDR'] == '192.168.1.143' OR $_SERVER['REMOTE_ADDR'] == '192.168.1.28' 
	OR $_SERVER['REMOTE_ADDR'] == '192.168.1.86' OR $_SERVER['REMOTE_ADDR'] == '192.168.1.45'
	OR $_SERVER['REMOTE_ADDR'] == '192.168.1.145' OR $_SERVER['REMOTE_ADDR'] == '192.168.1.158'
	OR $_SERVER['REMOTE_ADDR'] == '192.168.1.165' OR $_SERVER['REMOTE_ADDR'] == '192.168.1.72'
	OR $_SERVER['REMOTE_ADDR'] == '192.168.1.33') */
		$conn->debug= true;
	
	// cek flash
	$a_flash = Route::getFlashData();
	if(!empty($a_flash)) {
		foreach($a_flash as $k => $v)
			eval('$'."$k = '$v';");
	}
?>
