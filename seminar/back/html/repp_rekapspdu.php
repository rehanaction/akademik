<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
        // properti halaman
	$p_title = 'Cetak Laporan Data Pendaftar';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$l_nopendaftarmulai = UI::createTextBox('nopendaftarmulai',$r_nopendaftar);
	$l_nopendaftarakhir = UI::createTextBox('nopendaftarakhir',$r_nopendaftar);
	

	$a_input = array();
	$a_input[] = array(
           'label' => 'No Pendaftar Mulai',
           'input' => $l_nopendaftarmulai);
	$a_input[] = array(
           'label' => 'No Pendaftar Akhir',
           'input' => $l_nopendaftarakhir);
           
           
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
