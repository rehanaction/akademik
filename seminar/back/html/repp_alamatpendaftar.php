<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('combo'));
	
	// properti halaman
	$p_title = 'Cetak Alamat Pendaftar';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode');
	$l_sistemkuliah 	= uCombo::sistemKuliah($conn,$r_kosong,'',$nameid='sistemkuliah');
	$l_prodi 	= uCombo::unit($conn,$r_kosong,'','kodeunit');
	
	$a_input = array();
	$a_input[] = array(
           'label' => 'Prodi',
           'input' => $l_prodi);
	$a_input[] = array(
           'label' => 'Periode',
           'input' => $l_periode);
	$a_input[] = array(
           'label' => 'Basis',
           'input' => $l_sistemkuliah);
		   
	require_once($conf['view_dir'].'inc_repp.php');
?>

