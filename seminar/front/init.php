<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once('config.php');
	
	// session dan output buffer
	session_start();
	ob_start();
	
	// tampilkan doctype
	if(substr($i_page,0,4) != 'ajax' and $i_page != 'captured')
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'."\n";
	
	// gunakan helper
	require_once($conf['helpers_dir'].'cstr.class.php');
	require_once($conf['helpers_dir'].'date.class.php');
	require_once($conf['helpers_dir'].'modul.class.php');
	require_once($conf['helpers_dir'].'page.class.php');
	require_once($conf['helpers_dir'].'query.class.php');
	require_once($conf['helpers_dir'].'route.class.php');
	require_once($conf['helpers_dir'].'ui.class.php');
	
	require_once($conf['helpers_dir'].'akademik.class.php');
	require_once($conf['helpers_dir'].'seminar.class.php');
	
	// koneksi database
	$conn = Query::connect();
	if($_SERVER['REMOTE_ADDR']=='192.168.1.175' or $_SERVER['REMOTE_ADDR']=='192.168.1.75')
		$conn->debug = true;
	
	// cek flash
	$a_flash = Route::getFlashData();
	if(!empty($a_flash))
		extract($a_flash);
?>
