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
	$p_title = 'Laporan Jumlah Mahasiswa Belum KRS';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	

	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'input' => uCombo::jurusan($conn,$r_jurusan,$r_fakultas,'jurusan','',false));
	$a_input[] = array('label' => 'Periode Semester', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_input[] = array('label' => 'Angkatan', 'nameid' => 'angkatan', 'type' => 'S', 'option' => mCombo::angkatan($conn), 'default' => $r_angkatan);
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
