<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('statistik'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Statistik Keaktifan Pegawai';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = 'mStatistik';
	$p_reportpage = Route::getReportPage();
	
	$l_aktif = UI::createCheckBox('aktif[]',$p_model::filterAktif($conn),'',true,true,'checked');
		
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unitSave($conn,$r_kodeunit,'unit','',false));
	$a_input[] = array('label' => 'Status Aktif', 'input' => $l_aktif);

	require_once($conf['view_dir'].'inc_repp.php');
?>