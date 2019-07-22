<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kategoribeasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Daftar Kategori Beasiswa';
	$p_tbwidth = 500;
	$p_aktivitas = 'MASTER';
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No', 'width' => '20');
	$a_kolom[] = array('kolom' => 'kodekategori', 'label' => 'Kode', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namakategori', 'label' => 'Nama Kategori');
	
	$p_model = mKategoriBeasiswa;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	require_once($conf['view_dir'].'inc_list.php');
?>
