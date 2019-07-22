<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('gaji'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Rekapitulasi Pajak Tahunan';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	$r_month = (int)date('m');
	$r_tahun = date('Y');
	
	$p_model = 'mGaji';
	$p_reportpage = 'rep_gapajaktahunan';
		
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_kodeunit,'unit','',false));
	$a_input[] = array('label' => 'Tahun Periode Gaji', 'input' => uCombo::tahun($r_tahun,true,'tahun','',false));
	
	require_once($conf['view_dir'].'inc_repp.php');
?>