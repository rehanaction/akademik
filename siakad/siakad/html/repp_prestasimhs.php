<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Daftar Prestasi Mahasiswa';
	$p_tbwidth = 400;
	$p_aktivitas = 'LAPORAN';
	
	
	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'input' => uCombo::jurusan($conn,$r_jurusan,$r_fakultas,'jurusan','',false));
	require_once($conf['view_dir'].'inc_repp.php');
?>