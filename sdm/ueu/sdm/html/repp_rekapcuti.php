<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('cuti'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Rekapitulasi Cuti Pegawai';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	$r_month = (int)date('m');
	$r_tahun = date('Y');
	
	$p_model = 'mCuti';
	$p_reportpage = Route::getReportPage();
	
	$l_jenis = UI::createCheckBox('jenis[]',$p_model::filterJenis($conn),'',true,true,'checked');
		
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unitSave($conn,$r_kodeunit,'unit','',false));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::bulan($r_month,true,'bulan','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_input[] = array('label' => 'Jenis Pegawai', 'input' => $l_jenis);
	

	require_once($conf['view_dir'].'inc_repp.php');
?>