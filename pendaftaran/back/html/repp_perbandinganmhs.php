<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
        // properti halaman
	$p_title = 'Cetak laporan perbandingan mahasiswa';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$format_a= array('kolom' => 'tglawal', 'label' => 'Tanggal Awal', 'type' => 'D');
	$format_b= array('kolom' => 'tglakhir', 'label' => 'Tanggal Akhir', 'type' => 'D');
	
	$i_tanggalawal=uForm::getInput($format_a);
	$i_tanggalakhir=uForm::getInput($format_b);
	
	$a_input = array();
	$a_input[] = array('label' => 'Tanggal Awal','input' => $i_tanggalawal);
	$a_input[] = array('label' => 'Tanggal Akhir','input' => $i_tanggalakhir);
	
		   
	require_once($conf['view_dir'].'inc_repp.php');
?>

