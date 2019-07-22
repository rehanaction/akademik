<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('angkakredit'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Rekapitulasi KUM';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_tahun = empty($r_tahun) ? date('Y') : $r_tahun;
	
	$p_model = 'mAngkaKredit';
	$p_reportpage = 'rep_rekapitulasikum';
	
	//semester
	$sem =  UI::createSelect('sem',mAngkaKredit::PeriodeSemester(),'','ControlStyle',true,'',true);
	
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_kodeunit,'unit','',false));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::tahun($r_tahun,true,'tahun','',false).'&nbsp;'.$sem);

	if(empty($p_reportpage))
		$p_reportpage = Route::getReportPage();
		
	require_once($conf['view_dir'].'inc_repp.php');
?>