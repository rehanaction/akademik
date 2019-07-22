<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('universitas'));
	require_once(Route::getUIPath('combo'));

	// variabel request
	$r_jenisuniversitas = Modul::setRequest($_POST['isasing'],'asasing');
	
	//combo
	$l_jenisuniversitas = uCombo::jenisuniversitas($conn,$r_jenisuniversitas,'isasing','onchange="goSubmit()"',true);
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Universitas', 'combo' => $l_jenisuniversitas); 
	
	// properti halaman
	$p_title = 'Data Universitas';
	$p_tbwidth = 750;
	$p_aktivitas = 'WISUDA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mUniversitas;
	
	
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeuniversitas', 'label' => 'Kode Universitas');
	$a_kolom[] = array('kolom' => 'namauniversitas', 'label' => 'Nama Universitas');
	//$a_kolom[] = array('kolom' => 'isasing', 'label' => 'Luar / Dalam Negeri', 'type'=>'S', 'option'=>mCombo::statusuniversitas());
	$a_kolom[] = array('kolom' => 'n.namanegara', 'label' => 'Negara');   
	$a_kolom[] = array('kolom' => 'p.namapropinsi', 'label' => 'Propinsi');
	$a_kolom[] = array('kolom' => 'kota', 'label' => 'Nama Kota');
	
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
	if($r_jenisuniversitas!='') $a_filter[] = $p_model::getListFilter('isasing',$r_jenisuniversitas);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>