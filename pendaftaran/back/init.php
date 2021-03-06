<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once('config.php');
	
	// session dan output buffer
	session_start();
	ob_start();
	
	// tampilkan doctype
	if(substr($i_page,0,4) != 'ajax')
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'."\n";
	
	// gunakan helper
	require_once($conf['helpers_dir'].'ldap.php');
	require_once($conf['helpers_dir'].'cstr.class.php');
	require_once($conf['helpers_dir'].'date.class.php');
	require_once($conf['helpers_dir'].'modul.class.php');
	require_once($conf['helpers_dir'].'page.class.php');
	require_once($conf['helpers_dir'].'query.class.php');
	require_once($conf['helpers_dir'].'route.class.php');
	require_once($conf['helpers_dir'].'ui.class.php');
	require_once($conf['helpers_dir'].'pendaftaran.class.php');
	
	
	// koneksi database
	$conn = Query::connect();
	//$connh = Query::connect('h2h');
	
	/*
	// mengambil setting global
	Akademik::setGlobal($conn);
	*/
	// set debug
//	if($_SERVER['REMOTE_ADDR'] == '36.85.91.184' or $_SERVER['REMOTE_ADDR']== '66.96.234.212')
	//$conn->debug = true;
	
	// cek flash
	$a_flash = Route::getFlashData();
	if(!empty($a_flash)) {
		foreach($a_flash as $k => $v)
			eval('$'."$k = '$v';");
	}
?>
