<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastaktifitas'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Daftar Pelanggaran Dengan Sanksi';
	$p_tbwidth = 500;
	$p_aktivitas = 'BIODATA';
	$p_key = 'idjenispelanggaran';
	$p_dbtable = 'lv_pelanggaran';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mMastAktifitas;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idjenispelanggaran', 'label' => 'Kode', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'jenispelanggaran', 'label' => 'Nama Pelanggaran');
	
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'lv_sanksipelanggaran',$p_key);
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'idjenispelanggaran';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listQueryPelanggaran();
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	
	require_once(Route::getViewPath('inc_list'));
?>
