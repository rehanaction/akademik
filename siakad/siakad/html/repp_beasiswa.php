<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Daftar Penerima Beasiswa';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	
	$a_input = array();
	
	$a_input[] = array('label' => 'Sumber Beasiswa', 'input' => uCombo::beasiswa($conn,$r_beasiswa,'beasiswa','',false));
	$a_input[] = array('label' => 'Nama Beasiswa', 'input' => uCombo::namabeasiswa($conn,$r_namabeasiswa,'namabeasiswa','',false));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false).' s/d '.uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	require_once($conf['view_dir'].'inc_repp.php');
?>