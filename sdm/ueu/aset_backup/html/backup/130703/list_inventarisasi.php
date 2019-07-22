<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_insert = false;
	$c_edit = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	$c_delete = false;
	$p_printpage = false;
	
	// include
	require_once(Route::getModelPath('perolehan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_jenisperolehan = Modul::setRequest($_POST['jenisperolehan'],'JENISPEROLEHAN');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:250px;"',false);
	$l_jenisperolehan = uCombo::jenisperolehan($conn,$r_jenisperolehan,'jenisperolehan','onchange="goSubmit()" style="width:250px;"',true);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idperolehan', 'label' => 'ID.', 'width' => 40);
	$a_kolom[] = array('kolom' => 'tglperolehan', 'label' => 'Tgl. Perolehan', 'nosearch' => true, 'width' => 100, 'type' => 'D');
	$a_kolom[] = array('kolom' => 'unit', 'label' => 'Unit', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'jenisperolehan', 'label' => 'Jenis Perolehan', 'nosearch' => true, 'width' => 150);
	$a_kolom[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti', 'width' => 150);
	$a_kolom[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'align' => 'center', 'nosearch' => true, 'cekval' => '1', 'width' => 50);
	
	// properti halaman
	$p_title = 'Daftar Inventarisasi Aset';
	$p_tbwidth = 900;
	$p_aktivitas = 'Inventarisasi';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPerolehan;
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
	if(!empty($r_jenisperolehan)) $a_filter[] = $p_model::getListFilter('jenisperolehan',$r_jenisperolehan);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Jenis Perolehan', 'combo' => $l_jenisperolehan);

    require_once(Route::getViewPath('inc_list'));
?>


