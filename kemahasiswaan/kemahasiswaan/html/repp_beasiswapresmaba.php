<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('tingkatprestasi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_fakultas = Modul::setRequest($_POST['fakultas'],'FAKULTAS');
	
	// properti halaman
	$p_title = 'Daftar Pengajuan Beasiswa';
	$p_tbwidth = 500;
	$p_aktivitas = 'LAPORAN';
	

	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'input' => uCombo::unit($conn,$r_unit,'unit'));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester').' '.uCombo::tahun($r_tahun,true,'tahun'));
	$a_input[] = array('label' => 'Tingkat Prestasi', 'input' => uCombo::tingkatPrestasi($conn,$r_tingkat,'prestasi'));
	$a_input[] = array('label' => 'Provinsi', 'input' => uCombo::propinsi($conn,$r_propinsi,'propinsi'));

	require_once($conf['view_dir'].'inc_repp.php');
?>
