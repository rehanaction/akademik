<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = false; // $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = false; // $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	
	// koneksi database
	$connh = Query::connect('h2h');
	$connh->debug = $conn->debug;
	
	// properti halaman
	$p_title = 'Tagihan Her Registrasi';
	$p_tbwidth = 800;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mTagihan;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode');
	$a_kolom[] = array('kolom' => 'b.nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'semester', 'label' => 'Smt');
	$a_kolom[] = array('kolom' => 'status', 'label' => 'Status');
	$a_kolom[] = array('kolom' => 'tglmulai', 'label' => 'Mulai', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'tglselesai', 'label' => 'Selesai', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'billamount', 'label' => 'Jumlah', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'lunas', 'label' => 'Lunas');
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($connh,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	$a_data = $p_model::getPagerDataHer($connh,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>