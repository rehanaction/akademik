<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$connsia = Query::connect('akad');
	if($_SERVER['REMOTE_ADDR'] == "36.85.91.184") //ip public sevima
		$connsia->debug=true;
	
	// include
	require_once(Route::getModelPath('integrasi'));	
	require_once(Route::getModelPath('profile'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Grafik Profile Dosen Tetap dan Dosen Tidak Tetap';
	$p_tbwidth = 500;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = 'mProfile';
	$p_reportpage = Route::getReportPage();
		
	$l_periode = UI::createSelect('periode',mIntegrasi::getPeriodeSia($connsia),'','ControlStyle',true);
		
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unitSave($conn,$r_kodeunit,'unit','',false,true));
	$a_input[] = array('label' => 'Periode', 'input' => $l_periode);
	

	require_once($conf['view_dir'].'inc_repp.php');
?>
