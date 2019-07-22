<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('po'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	/*$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
	
	$year = date("Y"); 
	$mon = date("m"); 

	if(empty($r_tahun)) $r_tahun = $year;
	if(empty($r_bulan)) $r_bulan = $mon;*/
	
	// combo
	/*$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:250px;"',false);
	$l_tahun = uCombo::tahun($conn,$r_tahun,'tahun','onchange="goSubmit()"',false);
	$l_bulan = uCombo::bulan($conn,$r_bulan,'bulan','onchange="goSubmit()"',false);*/
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idpo', 'label' => 'ID.', 'width' => 40);
	$a_kolom[] = array('kolom' => 'tglpo', 'label' => 'Tgl. PO', 'width' => 90, 'type' => 'D');
	$a_kolom[] = array('kolom' => 'nopo', 'label' => 'No. PO', 'width' => 80, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'supplier', 'label' => 'Supplier');
	$a_kolom[] = array('kolom' => 'status', 'label' => 'Status', 'align' => 'center', 'width' => 40, 'nosearch' => true);
	
	// properti halaman
	$p_title = 'Daftar Purchase Order (PO)';
	$p_tbwidth = 750;
	$p_aktivitas = 'purchase order';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPo;
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
	/*if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_tahun) or !empty($r_bulan)) $a_filter[] = $p_model::getListFilter('periode','',$r_tahun,$r_bulan);*/
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	/*$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_bulan.'&nbsp;&nbsp;&nbsp;'.$l_tahun);*/

    require_once(Route::getViewPath('inc_list'));
?>
