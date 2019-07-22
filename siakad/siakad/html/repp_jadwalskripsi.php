<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Jadwal Pelaksanaan Ujian Skripsi';
	$p_tbwidth = 400;
	$p_aktivitas = 'LAPORAN';
	
	$t_tglawal = uForm::getInput(array('nameid' => 'tglawal', 'type' => 'D', 'default' => date('Y-m-d')));
	$t_tglakhir = uForm::getInput(array('nameid' => 'tglakhir', 'type' => 'D', 'default' => date('Y-m-d')));
	
	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'input' => uCombo::jurusan($conn,$r_jurusan,$r_fakultas,'jurusan','',false));
	$a_input[] = array('label' => 'Tanggal Ujian', 'input' => $t_tglawal.' - '.$t_tglakhir);
	require_once($conf['view_dir'].'inc_repp.php');
?>