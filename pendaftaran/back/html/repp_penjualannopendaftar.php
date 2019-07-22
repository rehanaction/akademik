<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('combo'));
	
        // properti halaman
	$p_title = 'Cetak laporan Penjualan No Pendaftar (Harian)';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$format_a= array('kolom' => 'tglawal', 'label' => 'Tanggal Awal', 'type' => 'D');
	$format_b= array('kolom' => 'tglakhir', 'label' => 'Tanggal Akhir', 'type' => 'D');
	$periode= array('kolom' => 'periode', 'label' => 'Periode', 'type' => 'S', 'option'=>mCombo::periode($conn));
	$sistemkuliah= array('kolom' => 'sistemkuliah', 'label' => 'Basis', 'type' => 'S', 'option'=>mCombo::sistemKuliah($conn), 'empty'=>true);
	
	$i_tanggalawal=uForm::getInput($format_a);
	$i_tanggalakhir=uForm::getInput($format_b);
	$i_periode=uForm::getInput($periode);
	$i_sistemkuliah=uForm::getInput($sistemkuliah);
	
	
	$a_input = array();
	$a_input[] = array('label' => 'Periode','input' => $i_periode);
	$a_input[] = array('label' => 'Basis','input' => $i_sistemkuliah);
	$a_input[] = array('label' => 'Tanggal Mulai','input' => $i_tanggalawal);
	$a_input[] = array('label' => 'Tanggal Selesai','input' => $i_tanggalakhir);
	
		   
	require_once($conf['view_dir'].'inc_repp.php');
?>

