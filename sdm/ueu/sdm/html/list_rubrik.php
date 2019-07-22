<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('bebandosen'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Daftar Rubrik BKD';
	$p_tbwidth = 750;
	$p_aktivitas = 'BIODATA';
	$p_key = 'idjeniskegiatan';
	$p_dbtable = 'ms_kegiatandosen';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mBebanDosen;
	
	// variabel request
	$r_kategori = Modul::setRequest($_POST['kategori'],'KATEGORI');
	$r_kategori = $p_model::setKategori($conn);
	$a_kategori = $p_model::getKategori($conn);
	$l_kategori = UI::createSelect('kategori',$a_kategori,$r_kategori,'ControlStyle',true,'onchange="goSubmit()"');
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodekegiatan', 'label' => 'Kode Kegiatan', 'width' => '150', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namakegiatan', 'label' => 'Kegiatan');
	$a_kolom[] = array('kolom' => 'isaktif', 'label' => 'is Aktif', 'width' => '100', 'align' => 'center');
	
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
	if(empty($r_sort)) $r_sort = 'idjeniskegiatan';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,'',$p_dbtable);
	$p_lastpage = Page::getLastPage();
	
	// mendapatkan data
	if(!empty($r_kategori)) $a_filter[] = $p_model::getListFilter('kategori',$r_kategori);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter,'',$p_dbtable);
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Kategori', 'combo' => $l_kategori);
	
	require_once(Route::getViewPath('inc_list'));
?>
