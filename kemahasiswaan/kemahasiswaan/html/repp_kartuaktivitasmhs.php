<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Cetak Kartu Aktivitas Mahasiswa';
	$p_tbwidth = 500;
	$p_aktivitas = 'LAPORAN';
	

	$a_input = array();
	$a_input[] = array('label' => 'Mahasiswa', 'nameid' => 'nim', 'type' => 'X', 'text' =>'mahasiswa','param'=>'acmahasiswa');
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
