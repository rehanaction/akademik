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
	$r_sumber = Modul::setRequest($_POST['sumber'],'SUMBER');
	$r_supplier = Modul::setRequest($_POST['supplier'],'SUPPLIER');

	$year = date("Y"); 
	$mon = date("m"); 

	if(empty($r_tahun)) $r_tahun = $year;
	if(empty($r_bulan2)) $r_bulan2 = $mon;
	
	$l_tahun = uCombo::tahun($conn,$r_tahun,'tahun','style="width:75px"',false);
	$l_bulan1 = uCombo::bulan($conn,$r_bulan1,'bulan1','style="width:90px"',false);
	$l_bulan2 = uCombo::bulan($conn,$r_bulan2,'bulan2','style="width:90px"',false);
	$l_sumber = uCombo::sumberdana($conn,$r_sumber,'sumber','style="width:90px"',true);
	$l_supplier = uCombo::supplier($conn,$r_supplier,'supplier','style="width:125px"',true);

	// properti halaman
	$p_title = 'Laporan Perolehan Barang';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_unit,'unit','style="width:300px"',false));
	$a_input[] = array('label' => 'Periode' , 'input' => $l_bulan1.' s/d '.$l_bulan2.' - '.$l_tahun);
	$a_input[] = array('label' => 'Sumber Dana', 'input' => uCombo::sumberdana($conn,$r_sumber,'sumber','style="width:300px"'));
	$a_input[] = array('label' => 'Supplier', 'input' => uCombo::supplier($conn,$r_supplier,'supplier','style="width:300px"'));

	require_once($conf['view_dir'].'inc_repp.php');
?>
