<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
        // properti halaman
	$p_title = 'Cetak Laporan Hasil Kelulusan';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode');
	$l_jalur 	= uCombo::jalur($conn,$r_jalur,'','jalur');
	$l_prodi = uCombo::jurusan($conn,$r_prodi,'','pilihan1');
	$l_tahap = uCombo::getTahapUjian($conn,$r_tahap,'','tahapujian');

	$a_input = array();
	$a_input[] = array(
           'label' => 'Periode',
           'input' => $l_periode);
	$a_input[] = array(
           'label' => 'Prodi Pilihan Pertama',
           'input' => $l_prodi);
    $a_input[] = array(
           'label' => 'Jalur Penerimaan',
           'input' => $l_jalur);
    $a_input[] = array(
           'label' => 'Tahap Ujian',
           'input' => $l_tahap);
	
	require_once($conf['view_dir'].'inc_repp.php');
?>

