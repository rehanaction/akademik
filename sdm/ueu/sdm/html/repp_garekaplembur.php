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
	$p_title = 'Rekapitulasi Lembur Pegawai';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = 'mGaji';
	$p_reportpage = Route::getReportPage();	
	
	$l_jenis = UI::createCheckBox('jenis[]',$p_model::filterJenis($conn),'',true,true,'checked');
			
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_kodeunit,'unit','',false));
	$a_input[] = array('label' => 'Periode Gaji', 'input' => UI::createSelect('periode',$p_model::getCPeriodeGaji($conn),$r_periode,'ControlStyle',true));
	$a_input[] = array('label' => 'Jenis Pegawai', 'input' => $l_jenis);
	

	require_once($conf['view_dir'].'inc_repp.php');
?>