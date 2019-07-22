<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
        // properti halaman
	$p_title = 'Rekap Peserta, Lulus , Registrasi';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode');
	$l_jalur 	= uCombo::jalur($conn,$r_jalur,'','jalur');
	$l_prodi = uCombo::unit($conn,$r_prodi,'','jurusan','',false);

	$a_input = array();
	$a_input[] = array(
           'label' => 'Periode',
           'input' => $l_periode);
	$a_input[] = array(
           'label' => 'Jurusan',
           'input' => $l_prodi);
    $a_input[] = array(
           'label' => 'Jalur Penerimaan',
           'input' => $l_jalur);
	
	require_once($conf['view_dir'].'inc_repp.php');
?>

