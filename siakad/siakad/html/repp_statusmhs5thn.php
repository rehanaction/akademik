<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Mahasiswa Berdasarkan Status Akademik';
	$p_tbwidth = 500;
	$p_aktivitas = 'LAPORAN';
	
    $t_angkatan1 = uForm::getInput(array('nameid' => 'angkatan1', 'type' => 'S', 'option' => mCombo::angkatan($conn)));
	$t_angkatan2 = uForm::getInput(array('nameid' => 'angkatan2', 'type' => 'S', 'option' => mCombo::angkatan($conn)));
	
	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'input' => uCombo::jurusan($conn,$r_jurusan,$r_fakultas,'jurusan','',false));
	//$a_input[] = array('label' => 'Periode', 'input' => $t_tahun1.' s/d '.$t_tahun2);
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::tahun($r_tahun1,true,'tahun1','',false).' s/d'.uCombo::tahun($r_tahun2,true,'tahun2','',false));
	

	require_once($conf['view_dir'].'inc_repp.php');
?>