<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('matakuliah'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_fakultas = Modul::setRequest($_POST['fakultas'],'FAKULTAS');
	
	// properti halaman
	$p_title = 'Daftar Polis Mahasiswa';
	$p_tbwidth = 500;
	$p_aktivitas = 'LAPORAN';
	

	$a_input = array();
	//$a_input[] = array('label' => 'Prodi', 'nameid' => 'jurusan', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'default' => $r_unit);
	//$a_input[] = array('label' => 'Periode Semester', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_input[] = array('label' => 'Mahasiswa', 'nameid' => 'nim', 'type' => 'X', 'text' =>'mahasiswa','param'=>'acmahasiswa');
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
