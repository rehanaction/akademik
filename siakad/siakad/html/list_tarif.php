<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
		
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('tarif'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_jenis = Modul::setRequest($_POST['jenis'],'JENISTARIF');
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	$l_jenis = uCombo::jenisTarif($conn,$r_jenis,'jenis','onchange="goSubmit()"',false);
	$l_periode = uCombo::periodeDaftar($conn,$r_periode,'periode','onchange="goSubmit()"',false);
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Data Tarif';
	$p_tbwidth = 750;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mTarif;
	
	// struktur view
	$a_kolom = array();
	
	$a_kolom[] = array('kolom' => 'namajenis', 'label' => 'Jenis');
	$a_kolom[] = array('kolom' => 'akademik.f_namaperiode(periodemasuk)', 'label' => 'Periode Daftar', 'alias' => 'periodemasuk');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit');
	$a_kolom[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur');
	$a_kolom[] = array('kolom' => 'isasing', 'label' => 'Mhs Asing', 'type' => 'C', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'jumlahtotal', 'label' => 'Jumlah', 'type' => 'N');
	
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
	if(!empty($r_jenis)) $a_filter[] = $p_model::getListFilter('jenistarif',$r_jenis);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	
	// mendapatkan data
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Jenis', 'combo' => $l_jenis);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	
	require_once(Route::getViewPath('inc_list'));
?>