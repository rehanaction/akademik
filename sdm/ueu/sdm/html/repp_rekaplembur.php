<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('presensi'));	
	require_once(Route::getModelPath('statistik'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Rekap Lembur Pegawai';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = 'mPresensi';
	$p_reportpage = 'rep_rekaplembur';
		
	$l_jenispeg = UI::createCheckBox('jenispeg[]',mStatistik::filterJenis($conn),'',true,true,'checked');
	
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_kodeunit,'unit','',false));
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tgl. Mulai', 'type' => D);
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tgl. Selesai', 'type' => D);
	$a_input[] = array('label' => 'Jenis Pegawai', 'input' => $l_jenispeg);

	require_once($conf['view_dir'].'inc_repp.php');
?>
