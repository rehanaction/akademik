<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	//$r_tglujian = Modul::setRequest($_POST['tglujian'],'TGLUJIAN');
	$r_periode=$r_tahun.$r_semester;
	$r_tglujian=date('Y-m-d');
		
	// properti halaman
	$p_title = 'Laporan Jadwal UTS/UAS';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'input' => uCombo::unit($conn,$r_unit,'unit','',false));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false));
	$a_tgl= array('kolom' => 'tglujian', 'type' => 'D');
	$a_input[] = array('label' => 'Tgl Ujian', 'input' => uForm::getInput($a_tgl,$r_tglujian));
	
	$a_laporan = array();
	$a_laporan['rep_jadwalujian&jenis=uts'] = 'Absensi UTS';
	$a_laporan['rep_jadwalujian&jenis=uas'] = 'Absensi UAS';
	
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
