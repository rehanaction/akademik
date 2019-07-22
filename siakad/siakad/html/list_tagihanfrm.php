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
	$p_title = 'Tagihan Formulir';
	$p_tbwidth = 800;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mTagihan;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'transactionid', 'label' => 'ID Trans');
	$a_kolom[] = array('kolom' => 'frmid', 'label' => 'ID Form');
	$a_kolom[] = array('kolom' => 'idpend', 'label' => 'ID Pendaftar');
	$a_kolom[] = array('kolom' => 'namapend', 'label' => 'Nama Pendaftar');
	$a_kolom[] = array('kolom' => 'accesstime', 'label' => 'Akses', 'type' => 'DT');
	$a_kolom[] = array('kolom' => 'billamount', 'label' => 'Jumlah', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'lunas', 'label' => 'Lunas');
	
	// ada aksi
	$r_act = $_POST['act'];
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
	$a_data = $p_model::getPagerDataFrm($connh,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>