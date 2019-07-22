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
	$p_title = 'Laporan Statistik Jabatan Akademik';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = 'mStatistik';
	$p_reportpage = Route::getReportPage();
	
	$l_jenis = UI::createCheckBox('jenis[]',$p_model::filterJenisDosen($conn),'',true,true,'checked');	
	$l_aktif = UI::createCheckBox('aktif[]',$p_model::filterAktif($conn),'',true,true,'checked');
	$l_aktifhb = UI::createCheckBox('aktifhb[]',$p_model::filterAktifHB($conn),'',true,true,'checked');
		
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unitSave($conn,$r_kodeunit,'unit','',false, true));
	$a_input[] = array('label' => 'Jenis Pegawai', 'input' => $l_jenis);
	$a_input[] = array('label' => 'Status Aktif', 'input' => $l_aktif);
	$a_input[] = array('label' => 'Status Aktif Homebase', 'input' => $l_aktifhb);
	

	require_once($conf['view_dir'].'inc_repp.php');
?>