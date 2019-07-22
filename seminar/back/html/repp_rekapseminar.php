<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('seminar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('jenisseminar'));
	
    // properti halaman
	$p_title = 'Cetak Laporan Rekap Seminar';
	$p_tbwidth = 450;
	//$p_aktivitas = 'LAPORAN';
	
	
	$a_tglawaldaftar = mSeminar::getArray($conn);
	$a_tglakhirdaftar = mSeminar::getArray($conn);
	$a_jenisseminar = mJenisSeminar::getArray($conn);
	$l_periode 	= mCombo::periode($conn);

	$a_input = array();
	$a_input[] = array('kolom' => 'kodejenisseminar','label' => 'Jenis Seminar', 'type' => 'S','option' => $a_jenisseminar);
	$a_input[] = array('kolom' => 'tglawaldaftar', 'label' => 'Tanggal Awal Kegiatan', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglakhirdaftar', 'label' => 'Tanggal Akhir Kegiatan', 'type' => 'D');
	$a_input[] = array('kolom' => 'periode','label' => 'Periode', 'type' => 'S','option' => $l_periode);
	   
	require_once($conf['view_dir'].'inc_repp.php');
?>

