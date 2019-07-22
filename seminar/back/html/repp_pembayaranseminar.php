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
	$p_title = 'Rekap Data Pembayaran';
	$p_tbwidth = 450;
	//$p_aktivitas = 'LAPORAN';
	
	$a_seminar = mSeminar::getArray($conn);
	$a_status = array('0' => 'Belum Lunas' , '-1' => ' Lunas');

	$a_input = array();
	$a_input[] = array('kolom' => 'idseminar', 'label' => 'Seminar', 'type' => 'S', 'option' => $a_seminar);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'S', 'option' => $a_status);
	
	   
	require_once($conf['view_dir'].'inc_repp.php');
?>

