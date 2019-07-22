<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	// variabel request
	$r_fakultas = Modul::setRequest($_POST['fakultas'],'FAKULTAS');
	// properti halaman
	$p_title = 'Laporan IPK Lulusan /Wisudawan Per-Fakultas ';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
			
	$a_input = array();
	$a_input[] = array('label' => 'Fakultas', 'input' => uCombo::fakultas($conn,$r_fakultas,'fakultas','onchange="goSubmit()"',false));
	$a_input[] = array('label' => 'Program Pendidikan', 'input' => uCombo::programPendidikan($conn,$r_programPendidikan,'progpend','',false));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	
	require_once($conf['view_dir'].'inc_repp.php');
?>