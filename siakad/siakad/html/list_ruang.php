<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('ruang'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','style="width:270px" onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Daftar Ruang Kelas';
	$p_tbwidth = 800;
	$p_aktivitas = 'UNIT';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mRuang;
	$statusruang=array('0'=>'Non Aktif','-1'=>'Aktif');
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'koderuang', 'label' => 'Kode Ruang');
	$a_kolom[] = array('kolom' => 'lokasi', 'label' => 'Nama Ruang');
	$a_kolom[] = array('kolom' => 'lantai', 'label' => 'Lantai');
	$a_kolom[] = array('kolom' => 'ipruang', 'label' => 'IP Ruangan');
	$a_kolom[] = array('kolom' => 'dayatampung', 'label' => 'Kapasitas');
	$a_kolom[] = array('kolom' => 'isaktif', 'label' => 'Status','type' => 'S', 'option' => $statusruang);
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
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	
	require_once(Route::getViewPath('inc_list'));
?>
