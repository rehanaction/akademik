<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');

	$year = date("Y"); 
	$mon = date("m"); 

	if(empty($r_tahun)) $r_tahun = $year;
	if(empty($r_bulan2)) $r_bulan2 = $mon;
	
	/*	-- Untuk yang memiliki Gudang Penyimpanan Khusus --	*/
	//$l_unit = uCombo::gudang($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	/*	-- Untuk yang memiliki Gudang di masing2 Unit --	*/
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($conn,$r_tahun,'tahun','onchange="goSubmit()"',false);
	$l_bulan1 = uCombo::bulan($conn,$r_bulan1,'bulan1','onchange="goSubmit()"',false);
	$l_bulan2 = uCombo::bulan($conn,$r_bulan2,'bulan2','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Laporan Aktivitas Habis Pakai';
	$p_tbresp = 'col-md-12 col-sm-12 col-lg-5';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	/*	-- Untuk yang memiliki Gudang Penyimpanan Khusus --	*/
	//$a_input[] = array('label' => 'Unit Penyimpanan', 'input' => uCombo::gudang($conn,$r_unit,'unit','style="width:300px"',false));
	/*	-- Untuk yang memiliki Gudang di masing2 Unit --	*/
	//$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_unit,'unit','style="width:300px"',false));
	$a_input[] = array('label' => 'Periode' , 'input' => $l_tahun);
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
