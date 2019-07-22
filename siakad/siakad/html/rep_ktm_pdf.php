<?php
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	//ini_set('display_errors',1);
	ob_clean();
	// hak akses
	Modul::getFileAuth();
	
	
	// include
	require_once(Route::getModelPath('laporanmhs'));
	require_once(Route::getModelPath('progpend'));
	require_once(Route::getUIPath('form'));
	//require_once($conf['includes_dir'].'fpdf/tcetak2.php');
	require_once($conf['includes_dir'].'fpdf/fpdf_ktm.php');
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_angkatan = (int)$_REQUEST['angkatan'];
	$r_npm = $_REQUEST['npm'];
	$r_format = $_REQUEST['format'];
	$r_tglberlaku = $_REQUEST['tglberlaku'];
	
	// properti halaman
	$p_title = 'Laporan KHS';
	$p_tbwidth = 720;
	
	$p_namafile = 'ktm_'.$r_kodeunit.'_'.$r_angkatan;
	$a_data = mLaporanMhs::getKtm($conn,$r_kodeunit,$r_angkatan,$r_npm);
	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);

	$t = new PDF_KTM("P","mm",array(85.7,54));
	$t->SetMargins(0,0);
	//$t->SetProtection(array('print'));
	$t->AliasNbPages();
	
	
foreach($a_data as $row){
	$t->AddPage();
	$t->SetAutoPageBreak(true, 0);
	
	
}

$t->SetDisplayMode(50);
$t->Output();


?>
