<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_periode=$r_tahun.$r_semester;
	$r_tglkuliah=date('Y-m-d');
		
	// properti halaman
	$p_title = 'Laporan Rekapitulasi Realisasi Perkuliahan Per Fakultas';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false));
	$a_tglawalkuliah= array('kolom' => 'tglawalkuliah', 'type' => 'D');
	$a_tglakhirkuliah= array('kolom' => 'tglakhirkuliah', 'type' => 'D');
	$a_input[] = array('label' => 'Tgl Kuliah', 'input' => uForm::getInput($a_tglawalkuliah,$r_tglkuliah).' - '.uForm::getInput($a_tglakhirkuliah,$r_tglkuliah));
	
	
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
