<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pegawaipenunjang'));
	require_once(Route::getUIPath('combo'));
	
	
	
	// properti halaman
	$p_title = 'Daftar Pegawai Penunjang Akademik';
	$p_tbwidth = 800;
	$p_aktivitas = 'UNIT';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPegawaiPenunjang;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nopegawai', 'label' => 'No Pegawai');
	$a_kolom[] = array('kolom' => 'namapegawai', 'label' => 'Nama Pegawai');
	$a_kolom[] = array('kolom' => 'nohp', 'label' => 'No. HP');
	$a_kolom[] = array('kolom' => 'notelephon', 'label' => 'No. Telephon');
	$a_kolom[] = array('kolom' => 'email', 'label' => 'Email');
	$a_kolom[] = array('kolom' => 'isasisten', 'label' => 'Asisten ?','type'=>'C');
	
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
	

	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	
	
	require_once(Route::getViewPath('inc_list'));
?>
