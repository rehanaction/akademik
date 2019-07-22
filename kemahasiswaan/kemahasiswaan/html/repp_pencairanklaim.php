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
	$p_title = 'Cetak Rekap Klaim Asuransi';
	$p_tbwidth = 550;
	$p_aktivitas = 'LAPORAN';

	$t_tglawal = uForm::getInput(array('nameid' => 'tglawal', 'type' => 'D', 'default' => date('Y-m-d')));
	$t_tglakhir = uForm::getInput(array('nameid' => 'tglakhir', 'type' => 'D', 'default' => date('Y-m-d')));


	$a_input = array();
	$a_input[] = array('label' => 'Fakultas', 'nameid' => 'jurusan', 'type' => 'S', 'option' => mCombo::fakultas($conn), 'default' => $r_unit);
	$a_input[] = array('label' => 'Tanggal Pengajuan', 'input' => $t_tglawal.' - '.$t_tglakhir);

	require_once($conf['view_dir'].'inc_repp.php');
?>
