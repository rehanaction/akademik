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
	$p_title = 'Laporan Rekapitulasi Mahasiswa Per Semester';
	$p_tbwidth = 400;
	$p_aktivitas = 'LAPORAN';
	

	$a_input = array();
	$a_input[] = array('label' => 'Fakultas', 'input' => uCombo::fakultas($conn,$r_fakultas,'unit','',true));
	$a_input[] = array('label' => 'Periode Semester', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_input[] = array('label' => 'Status Mahasiswa', 'input' => uCombo::statusmhs($conn,$r_status,'statusmhs','',false));
	
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
