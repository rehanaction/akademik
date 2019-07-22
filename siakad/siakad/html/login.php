<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// ambil session gate
	$data = rawurldecode($_POST['sessdata']);
	Modul::getSessionData($data);
	
	// tulis data default
	$periode = Akademik::getPeriode();
	
	Modul::setRequest(substr($periode,0,4),'TAHUN');
	Modul::setRequest(substr($periode,-1),'SEMESTER');
	
	// filter tree default
	$_SESSION[SITE_ID]['EX']['list_mahasiswa']['FILTERTREE'][] = 'm.statusmhs:A';
	
	// redirect ke home
	Route::navigate('home');
?>