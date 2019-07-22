<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('loggenerate'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idloggen', 'label' => 'ID','align'=>'center');
	$a_kolom[] = array('kolom' => 'periodetagihan', 'label' => 'Periode','align'=>'center');
	$a_kolom[] = array('kolom' => 'jenistagihan', 'label' => 'Jenis Tagihan','align'=>'center');
	$a_kolom[] = array('kolom' => 'bulantahun', 'label' => 'Bulan','align'=>'center');
	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Kodeunit');
	$a_kolom[] = array('kolom' => 'jalurpenerimaan', 'label' => 'jalur Penerimaan','align'=>'center');
	$a_kolom[] = array('kolom' => 'jml', 'label' => 'Jumlah','align'=>'center');
	$a_kolom[] = array('kolom' => 'isgen', 'label' => 'Dibatalkan?', 'type' => 'C', 'option' => array('V' => ''),'align'=>'center','width'=>'6%');
	
	// properti halaman
	$p_title = 'Daftar History Generate Tagihan';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORI';
	
	$p_model = mLoggenerate;
	$p_colnum = count($a_kolom);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	require_once(Route::getViewPath('inc_list'));
?>	