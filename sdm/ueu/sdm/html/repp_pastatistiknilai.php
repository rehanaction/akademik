<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('pa'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Statistik Hasil Penilaian Kinerja';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = 'mPa';
	$p_reportpage = Route::getReportPage();
	
	$l_periode = UI::createSelect('periode',$p_model::getCPeriode($conn),'','ControlStyle',true);
		
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unitSave($conn,$r_kodeunit,'unit','',false));
	$a_input[] = array('label' => 'Periode', 'input' => $l_periode);	

	require_once($conf['view_dir'].'inc_repp.php');
?>