<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');

	$year = date("Y"); 

	if(empty($r_tahun)) $r_tahun = $year;
	
	$l_tahun = uCombo::tahun($conn,$r_tahun,'tahun','tahun','style="width:75px"',false);
	
	// properti halaman
	$p_title = 'Laporan Rekap Perolehan Aset Tahunan';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	$a_input[] = array('label' => 'Periode' , 'input' => $l_tahun);

	require_once($conf['view_dir'].'inc_repp.php');
?>
