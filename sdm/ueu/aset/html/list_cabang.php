<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('cabang'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	//$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	//$l_unit = uCombo::unit($conn,$r_unit,'unit','style="width:270px" onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Daftar Kantor Cabang';
	$p_tbwidth = 800;
	$p_aktivitas = 'Cabang';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mCabang;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idcabang', 'label' => 'ID.');
	$a_kolom[] = array('kolom' => 'namacabang', 'label' => 'Nama Cabang');
	$a_kolom[] = array('kolom' => 'alamat', 'label' => 'Alamat');
	$a_kolom[] = array('kolom' => 'kota', 'label' => 'Kota ');
	$a_kolom[] = array('kolom' => 'provinsi', 'label' => 'Provinsi');
	
	$p_colnum = count($a_kolom)+1;
	
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
	//if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	//$a_filtercombo = array();
	//$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	
	require_once(Route::getViewPath('inc_list'));
?>
