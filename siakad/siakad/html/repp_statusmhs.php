<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Rekapitulasi Mahasiswa Berdasarkan Status Akademik';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
    $t_angkatan1 = uForm::getInput(array('nameid' => 'angkatan1', 'type' => 'S', 'option' => mCombo::angkatan($conn)));
	$t_angkatan2 = uForm::getInput(array('nameid' => 'angkatan2', 'type' => 'S', 'option' => mCombo::angkatan($conn)));
	
	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'nameid' => 'unit', 'type' => 'S', 'option' => mCombo::unit($conn,false,true), 'default' => $r_unit);
	//$a_input[] = array('label' => 'Angkatan', 'input' => $t_angkatan1.' s/d '.$t_angkatan2);
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	

	require_once($conf['view_dir'].'inc_repp.php');
?>