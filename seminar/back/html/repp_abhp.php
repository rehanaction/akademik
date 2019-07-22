<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
        // properti halaman
	$p_title = 'Album Bukti Hadir Peserta (ABHP)';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	
	$a_input = array();

	$a_input[] = array(
                           'label' => 'Ruang',
                           'input' => uCombo::ruang($conn, $r_ruang,true,'ruang','',false));
        $a_input[] = array(
                           'label' => 'Jalur Penerimaan',
                           'input' => uCombo::periode($conn, $r_periode,true,'periode','',false).' '.uCombo::jalur($conn, $r_jalur,true,'jalur','',false).' '.uCombo::gelombang($conn, $r_gelombang,true,'gelombang','',false));
	require_once($conf['view_dir'].'inc_repp.php');
?>

