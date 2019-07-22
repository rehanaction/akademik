<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Daftar Jenis Jabatan';
	$p_tbwidth = 900;
	$p_aktivitas = 'STRUKTUR';
	$p_dbtable = "ms_jabatan";
	$p_key = 'idjabatan';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mMastKepegawaian;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idjabatan', 'label' => 'Kode', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namajabatan', 'label' => 'Nama Jabatan');
	$a_kolom[] = array('kolom' => 'level', 'label' => 'Level', 'align' => 'center', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'namaeselon', 'label' => 'Eselon', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namapangkat', 'label' => 'Pangkat Min.', 'filter' => "'('||p.golongan||') ' || p.namapangkat");
	
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'level';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listJabatan();
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	
	require_once(Route::getViewPath('inc_list'));
?>
