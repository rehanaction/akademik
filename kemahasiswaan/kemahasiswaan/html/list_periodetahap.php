<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('periodetahap'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Perode Tahap Kegiatan';
	$p_tbwidth = 800;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPeriodeTahap;
	
	// struktur view
	$a_program = mCombo::programPendidikan($conn);
	
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodejenjang', 'label' => 'Jenjang');
	$a_kolom[] = array('kolom' => 'namatahap', 'label' => 'Tahap Kegiatan');
	$a_kolom[] = array('kolom' => 'semsbawah', 'label' => 'Semester Bawah');
	$a_kolom[] = array('kolom' => 'semsatas', 'label' => 'Semester Atas');
	
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
	$a_filter = CStr::removeSpecial($_POST['tfilter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	$a_data = $p_model::getListPeriodeTahap($conn,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>
