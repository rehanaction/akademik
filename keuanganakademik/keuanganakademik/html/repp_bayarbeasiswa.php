<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Pembayaran - Beasiswa';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	       
	$a_input = array();
	$a_input['periode'] = array('kolom' => 'periode', 'label' => 'Periode Tagihan', 'type' => 'S', 'option' => mAkademik::getArrayperiode($conn));
	$a_input['sistemkuliah'] = array('kolom' => 'sistemkuliah', 'label' => 'Basis', 'type' => 'S', 'option' => array('' => 'Semua') + mAkademik::getArraySistemKuliahCombo($conn));
	$a_input['unit'] = array('kolom' => 'unit', 'label' => 'Unit', 'type' => 'S', 'option' => mAkademik::getArrayunit($conn));
	$a_input['angkatan'] = array('kolom' => 'angkatan', 'label' => 'Angkatan', 'type' => 'S', 'option' => array('' => 'Semua') + mCombo::tahun_angkatan());
	$a_input['statusmhs'] = array('kolom' => 'statusmhs', 'label' => 'Status Mhs', 'type' => 'S', 'option' => mAkademik::getArrayStatusMhs($conn));
	
	require_once(Route::getViewPath('inc_repp'));
?>