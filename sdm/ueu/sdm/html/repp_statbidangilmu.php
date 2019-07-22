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
	$p_title = 'Laporan Statistik Bidang Ilmu';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = 'mStatistik';
	$p_reportpage = Route::getReportPage();
	
	$l_fungsional = UI::createCheckBox('fungsional[]',$p_model::filterFungsional($conn),'',true,true,'checked');
	$l_pendidikan = UI::createCheckBox('pendidikan[]',$p_model::filterPendidikan($conn),'',true,true,'checked');
	$l_sesuai = UI::createCheckBox('sesuai',array('Y' => 'Sesuai Bidang'),'',true,true,'checked');
		
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unitSave($conn,$r_kodeunit,'unit','',false,true));
	$a_input[] = array('label' => 'Jabatan Akademik', 'input' => $l_fungsional);
	$a_input[] = array('label' => 'Pendidikan', 'input' => $l_pendidikan);
	$a_input[] = array('label' => 'Kesesuaian Bidang', 'input' => $l_sesuai);
	

	require_once($conf['view_dir'].'inc_repp.php');
?>