<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('translog'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	// $a_kolom[] = array('kolom' => 'idtranslog', 'label' => 'ID','align'=>'center');
	// $a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode','align'=>'center');
	$a_kolom[] = array('kolom' => 't_updatetime', 'label' => 'Tgl','align'=>'center','type'=>'DT','width'=>140);
	$a_kolom[] = array('kolom' => 'typeinq', 'label' => 'Trans','align'=>'center');
	$a_kolom[] = array('kolom' => 'jenistagihan', 'label' => 'Jenis','align'=>'center');
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'NIM','align'=>'center');
	// $a_kolom[] = array('kolom' => 'ish2h', 'label' => 'H2H?', 'type' => 'C', 'option' => array('1' => ''),'align'=>'center');
	$a_kolom[] = array('kolom' => 'companycode', 'label' => 'Comp','align'=>'center');
	$a_kolom[] = array('kolom' => 'channelid', 'label' => 'Channel','align'=>'center');
	$a_kolom[] = array('kolom' => 'refno', 'label' => 'No. Ref.','align'=>'center');
	$a_kolom[] = array('kolom' => 'ket', 'label' => 'Keterangan');
	
	// properti halaman
	$p_title = 'Daftar History Transaksi';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORI';
	
	$p_model = mTranslog;
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