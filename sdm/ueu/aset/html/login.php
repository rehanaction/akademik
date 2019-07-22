<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// ambil session gate
	$data = rawurldecode($_POST['sessdata']);
	Modul::getSessionData($data);

//print_r($_POST['sessdata']);
//exit;
	
	// redirect ke home
	Route::navigate('home');
?>
