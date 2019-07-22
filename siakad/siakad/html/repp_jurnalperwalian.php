<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Daftar Jurnal Perwalian';
	$p_tbwidth = 400;
	$p_aktivitas = 'LAPORAN';
	
	$r_jurusan = Modul::getRequest('UNIT');
	$r_semester = Modul::getRequest('SEMESTER');
	$r_tahun = Modul::getRequest('TAHUN');
	
	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'input' => uCombo::jurusan($conn,$r_jurusan,$r_fakultas,'jurusan','',false));
	//$a_input[] = array('label' => 'Dosen', 'input' => uCombo::dosen($conn,$r_dosen,$r_fakultas,'dosen','',false));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	require_once($conf['view_dir'].'inc_repp.php');
?>
