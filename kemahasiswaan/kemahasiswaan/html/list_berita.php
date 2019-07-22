<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$c_validasi = $a_auth['canother']['V'];
	
	// include
	require_once(Route::getModelPath('berita'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Publikasi';
	$p_tbwidth = 700;
	$p_aktivitas = 'BERITA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mBerita;
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'waktuposting', 'label' => 'Tanggal', 'type' => 'D', 'width' => 80, 'align' => 'center');
	$a_kolom[] = array('kolom' => 'jenis', 'label' => 'Jenis', 'type' => 'S', 'option' => $p_model::jenisBerita(), 'width' => 80);
	$a_kolom[] = array('kolom' => 'uc.userdesc', 'alias' => 'namacreator', 'label' => 'Pembuat', 'width' => 180);
	$a_kolom[] = array('kolom' => 'judulberita', 'label' => 'Judul');
	$a_kolom[] = array('kolom' => 'case when f.validator is null then 0 else -1 end', 'alias' => 'valid', 'label' => 'Tampil', 'type' => 'C', 'option' => array('-1' => ''), 'align' => 'center');
	
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
	if(!$c_validasi) $a_filter[] = $p_model::getListFilter('valid',-1);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>