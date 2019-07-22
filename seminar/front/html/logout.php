<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include model
	require_once(Route::getModelPath('pendaftar','front'));
	
	// properti halaman
	$p_model = mPendaftarFront;
	
	// logout
	$p_model::logout();
	
	// flash ke login
	$a_flash = array();
	$a_flash['posterr'] = false;
	$a_flash['postmsg'] = 'Anda berhasil logout, silahkan login kembali jika ingin mendaftar seminar';
	
	Route::setFlashData($a_flash,'login_form');
?>