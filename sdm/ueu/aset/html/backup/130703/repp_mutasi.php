<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// properti halaman
	$p_title = 'Laporan Mutasi Barang';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_unit,'unit','style="width:300px"',false));
	//$a_input[] = array('label' => 'Jenis Rawat', 'input' => uCombo::jenisrawat($conn,$r_jnsrawat,'jenisrawat','style="width:175px"',true));

	require_once($conf['view_dir'].'inc_repp.php');
?>
