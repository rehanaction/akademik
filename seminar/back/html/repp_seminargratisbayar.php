<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('seminar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
    // properti halaman
	$p_title = 'Rekap Seminar Gratis / Berbayar';
	$p_tbwidth = 450;
	//$p_aktivitas = 'LAPORAN';
	
	$a_periode 	= mCombo::periode($conn);
	$a_seminar = mSeminar::getArray($conn);
	$a_peserta = array('M' =>'Mahasiswa','P' =>'Pegawai','U' =>'Umum');
	$a_tarif = array('0' =>'Berbayar','1' =>'Gratis');

	$a_input = array();
	$a_input[] = array('kolom' => 'peserta', 'label' => 'Jenis Peserta', 'type' => 'S', 'option' => $a_peserta);
	$a_input[] = array('kolom' => 'jenis', 'label' => 'Jenis Tarif', 'type' => 'S', 'option' => $a_tarif);
	$a_input[] = array('kolom' => 'periode','label' => 'Periode', 'type' => 'S', 'option' => $a_periode);
	   
	require_once($conf['view_dir'].'inc_repp.php');
?>

