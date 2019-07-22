<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(Akademik::isMhs())
		$r_key = Modul::getUserName();
	else
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
	
	// properti halaman
	$p_title = 'Status Perwalian Mahasiswa';
	$p_tbwidth = 700;
	$p_aktivitas = 'ABSENSI';
	$p_detailpage = Route::getDetailPage();
	$p_headermhs = true;
	$p_mhspage = true;
	
	
	$p_model = mPerwalian;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'f_namaperiode(p.periode)', 'alias' => 'namaperiode', 'label' => 'Periode');
	$a_kolom[] = array('kolom' => 's.namastatus', 'label' => 'Status');
	$a_kolom[] = array('kolom' => 'g.nama||p.nipdosenwali', 'alias' => 'dosenwali', 'label' => 'Dosen Wali');
	$a_kolom[] = array('kolom' => 'p.tglsk', 'label' => 'Tanggal SK', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'p.nosk', 'label' => 'Nomor SK');
	$a_kolom[] = array('kolom' => 'p.alasancuti', 'label' => 'Alasan');
	
	
	// mendapatkan data
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort);
	
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	
	// membuat filter
	require_once(Route::getViewPath('inc_list'));
	 
?>
<html>
<head>
	<link href="style/officexp.css" rel="stylesheet" type="text/css"> 
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
</html>
