<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit = Modul::getRequest('UNIT');
	$r_semester = Modul::getRequest('SEMESTER');
	$r_tahun = Modul::getRequest('TAHUN');
	
	// properti halaman
	$p_title = 'Laporan Kelas';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'nameid' => 'unit', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'default' => $r_unit);
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	
	$a_laporan = array();
	$a_laporan['rep_jurnal'] = 'Jurnal';
	$a_laporan['rep_jurnalblmabsen'] = 'Jurnal Blm. Absen';
	$a_laporan['rep_absensi'] = 'Absensi';
	//$a_laporan['rep_pftgsuts'] = 'Pf-Tgs-UTS';
	//$a_laporan['rep_absensiuas&uts=1'] = 'UTS';
	//$a_laporan['rep_absensiuas'] = 'UAS';
	$a_laporan['rep_nilai'] = 'Nilai Akhir';
	$a_laporan['rep_nilaimhs'] = 'Daftar NIlai';
	$a_laporan['rep_allkrsmhs'] = 'Daftar KRS';
	$a_laporan['rep_statusnilai'] = 'Status Penilaian';
	require_once($conf['view_dir'].'inc_repp.php');
?>
