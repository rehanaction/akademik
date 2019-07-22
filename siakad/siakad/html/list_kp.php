<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kerjapraktek'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	// combo
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Data Kuliah Kerja Nyata (KKN)';
	$p_tbwidth = 800;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mKerjaPraktek;
	
	// struktur view
	$a_kolom = array();
	
	$a_kolom[] = array('kolom' => 'judulkp', 'label' => 'Judul');
	$a_kolom[] = array('kolom' => 'tglmulai', 'label' => 'Mulai', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'tglselesai', 'label' => 'Selesai', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'namaperusahaan', 'label' => 'Perusahaan');
	$a_kolom[] = array('kolom' => 'statuskp', 'label' => 'Status', 'type' => 'S', 'option' => $p_model::status());
	
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
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	
	require_once(Route::getViewPath('inc_list'));
?>