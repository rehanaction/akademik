<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = false;
	
	// include
	require_once(Route::getModelPath('opname'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:250px;"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idopname', 'label' => 'ID.', 'width' => 40);
	$a_kolom[] = array('kolom' => 'tglopname', 'label' => 'Tgl. Opname', 'width' => 100, 'type' => 'D');
	$a_kolom[] = array('kolom' => 'unit', 'label' => 'Unit');
	$a_kolom[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'width' => 250);
	$a_kolom[] = array('kolom' => 'status', 'label' => 'status', 'align' => 'center', 'width' => 40);
	
	// properti halaman
	$p_title = 'Daftar Opname';
	$p_tbwidth = 800;
	$p_aktivitas = 'opname';
	$p_detailpage = Route::getDetailPage();
	$p_tfiltersize = 16;
	
	$p_model = mOpname;
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
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
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);

    require_once(Route::getViewPath('inc_list'));
?>
