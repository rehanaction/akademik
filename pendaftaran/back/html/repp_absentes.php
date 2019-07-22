<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
        // properti halaman
	$p_title = 'Cetak Absensi Peserta Tes';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode');
	$l_jalur 	= uCombo::jalur($conn,$r_jalur,'','jalur');
	$a_input= array('kolom' => 'tgltes', 'label' => 'Tanggal Tes', 'type' => 'D');
	$i_tanggal=uForm::getInput($a_input,$r_tgltes);
	$l_tahap = uCombo::getTahapSeleksi($conn,$r_tahap,'','tahapujian');
	
	$a_input = array();
	$a_input[] = array(
           'label' => 'Periode',
           'input' => $l_periode);
    $a_input[] = array(
           'label' => 'Jalur Penerimaan',
           'input' => $l_jalur);
	$a_input[] = array(
           'label' => 'Tahap Ujian',
           'input' => $l_tahap);
	$a_input[] = array(
           'label' => 'Tanggal Tes',
           'input' => $i_tanggal);
	
		   
	require_once($conf['view_dir'].'inc_repp.php');
?>

