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
	$p_title = 'Cetak Rekapitulasi Mahasiswa Baru';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$format_a= array('kolom' => 'tglawal', 'label' => 'Tanggal Awal', 'type' => 'D');
	$format_b= array('kolom' => 'tglakhir', 'label' => 'Tanggal Akhir', 'type' => 'D');
	
	
	$i_tanggalawal=uForm::getInput($format_a);
	$i_tanggalakhir=uForm::getInput($format_b);
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode');
	$l_sistemkuliah 	= uCombo::sistemKuliah($conn,$r_kosong,'','sistemkuliah');
	
	$a_input = array();
	$a_input[] = array('label' => 'Tanggal Mulai Registrasi','input' => $i_tanggalawal);
	$a_input[] = array('label' => 'Tanggal Selesai Registrasi','input' => $i_tanggalakhir);
	$a_input[] = array(
           'label' => 'Periode',
           'input' => $l_periode);
	$a_input[] = array(
           'label' => 'Sistem Kuliah',
           'input' => $l_sistemkuliah);
		   
	require_once($conf['view_dir'].'inc_repp.php');
?>

