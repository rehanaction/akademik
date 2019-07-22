<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('gaji'));	
	require_once(Route::getModelPath('pinjaman'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Angsuran Pinjaman Karyawan';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	$r_month = (int)date('m');
	$r_tahun = date('Y');
	
	$p_model = 'mPinjaman';
	$p_reportpage = 'rep_gaangsuran';
	
	$l_jenispinjaman = UI::createCheckBox('jenispinjaman[]',$p_model::filterJenisPinjaman($conn),'',true,true,'checked');
		
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_kodeunit,'unit','',false));
	$a_input[] = array('label' => 'Periode Gaji', 'input' => uCombo::bulan($r_month,true,'bulan','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_input[] = array('label' => 'Jenis Pinjaman', 'input' =>  $l_jenispinjaman);
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
