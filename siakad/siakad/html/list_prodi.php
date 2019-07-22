<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Data Informasi Prodi';
	$p_tbwidth = 600;
	$p_aktivitas = 'UNIT';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mUnit;
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'nama_program_studi', 'label' => 'Prodi');
	$a_kolom[] = array('kolom' => 'kode_jenjang_studi', 'label' => 'Jenjang');
	$a_kolom[] = array('kolom' => 'email', 'label' => 'Email');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteProdi($conn,$r_key);
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
	$sql = $p_model::listQueryProdi();
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>