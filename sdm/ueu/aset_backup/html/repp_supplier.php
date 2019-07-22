<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_jenissupplier = Modul::setRequest($_POST['jenissupplier'],'JENISSUPP');

	// properti halaman
	$p_title = 'Laporan Daftar Supplier';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$i_jenis = uCombo::combo(mCombo::jenissupplier($conn),$r_jenissupplier,'jenissupplier','style="width:250px"',true,'jenis supplier');

	$a_input = array();
	$a_input[] = array('label' => 'Jenis Supplier', 'input' => $i_jenis);
	
	require_once($conf['view_dir'].'inc_repp.php');
?>

