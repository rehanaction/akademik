<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Daftar Mahasiswa Berdasarkan IPK Tertinggi';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	
	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'input' => uCombo::jurusan($conn,$r_jurusan,$r_fakultas,'jurusan','',false));
	$a_input[] = array('label' => 'Angkatan', 'input' => uCombo::angkatan($conn,$r_angkatan,'angkatan','',false));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	

	require_once($conf['view_dir'].'inc_repp.php');
?>