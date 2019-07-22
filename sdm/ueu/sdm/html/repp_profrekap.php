<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('profile'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Rekapitulasi ILBD';
	$p_tbwidth = 500;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = 'mProfile';
	$p_reportpage = Route::getReportPage();
	
	$l_periode = UI::createSelect('periode',$p_model::getPeriodeYear($conn),'','ControlStyle',true);
		
	$a_input = array();
	$a_input[] = array('label' => 'Periode', 'input' => $l_periode);
	

	require_once($conf['view_dir'].'inc_repp.php');
?>