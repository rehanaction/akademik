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
	$p_title = 'Laporan Statistik Hubungan Kerja';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = 'mStatistik';
	$p_reportpage = Route::getReportPage();
	
	$l_hubungan = UI::createCheckBox('hubungan[]',$p_model::filterHubungan($conn),'',true,true,'checked');
	$l_jenis = UI::createCheckBox('jenis[]',$p_model::filterJenis($conn),'',true,true,'checked');
	$l_aktif = UI::createCheckBox('aktif[]',$p_model::filterAktif($conn),'',true,true,'checked');
		
	$a_input = array();
	$a_input[] = array('label' => 'Unit Kerja', 'input' => uCombo::unitSave($conn,$r_kodeunit,'unit','',true));
	$a_input[] = array('label' => 'Unit Homebase', 'input' => uCombo::unitSave($conn,$r_kodeunit,'unithb','',true,true));
	$a_input[] = array('label' => 'Hubungan Kerja', 'input' => $l_hubungan);
	$a_input[] = array('label' => 'Jenis Pegawai', 'input' => $l_jenis);
	$a_input[] = array('label' => 'Status Aktif', 'input' => $l_aktif);
	

	require_once($conf['view_dir'].'inc_repp.php');
?>