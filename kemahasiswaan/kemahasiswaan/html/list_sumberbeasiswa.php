<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Data Sumber Beasiswa';
	$p_tbwidth = 800;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mSumberBeasiswa;
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'kodesumber', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'namasumber', 'label' => 'Nama Sumber');
	$a_kolom[] = array('kolom' => 'alamat', 'label' => 'Alamat');
	$a_kolom[] = array('kolom' => 'namakota', 'label' => 'Kota');
	$a_kolom[] = array('kolom' => 'telp', 'label' => 'Telp');
	$a_kolom[] = array('kolom' => 'email', 'label' => 'Email');
	$a_kolom[] = array('kolom' => 'contactperson', 'label' => 'Contact Person');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
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
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>