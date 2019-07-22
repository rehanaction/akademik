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
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
//	$r_supplier = Modul::setRequest($_POST['supplier'],'SUPPLIER');
	
	$year = date("Y"); 
	$mon = date("m"); 

	if(empty($r_tahun)) $r_tahun = $year;
	if(empty($r_bulan)) $r_bulan = $mon;

	$l_tahun = uCombo::tahun($conn,$r_tahun,'tahun','onchange="goSubmit()"',false);
	$l_bulan = uCombo::bulan($conn,$r_bulan,'bulan','onchange="goSubmit()"',false);

	// properti halaman
	$p_title = 'Laporan Perawatan';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_unit,'unit','style="width:300px"',false));
	$a_input[] = array('label' => 'Periode' , 'input' => $l_bulan.'&nbsp;&nbsp;&nbsp;'.$l_tahun);
//	$a_input[] = array('label' => 'Supplier', 'input' => uCombo::supplier($conn,$r_unit,'unit','style="width:300px"'));
	$a_input[] = array('label' => 'Jenis Rawat', 'input' => uCombo::jenisrawat($conn,$r_jnsrawat,'jenisrawat','style="width:175px"',true));

	require_once($conf['view_dir'].'inc_repp.php');
?>
