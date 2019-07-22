<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = true; // $a_auth['canupdate']; // harusnya gini sih...
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('ta'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	// $r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	// $l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Daftar Skripsi Mahasiswa';
	$p_tbwidth = 750;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mTa;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'N I M');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	// $a_kolom[] = array('kolom' => 'up.namaunit', 'alias' => 'namafakultas', 'label' => 'Fakultas');
	$a_kolom[] = array('kolom' => 'r.kodeunit', 'label' => 'Jur');
	//$a_kolom[] = array('kolom' => 'r.semestermhs', 'label' => 'Smt'); 
	$a_kolom[] = array('kolom' => 'topikta', 'label' => 'Topik TA');
	// $a_kolom[] = array('kolom' => 'tahapta', 'label' => 'Tahap');
	$a_kolom[] = array('kolom' => 'namautama', 'label' => 'Dosen Pembimbing');
	$a_kolom[] = array('kolom' => 'statusta', 'label' => 'Status');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$a_filtercol = $p_model::getArrayListFilterCol();
	$a_filtercol += $p_model::getArrayListFilter();
	
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$a_filtercol);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	// if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!Akademik::isDosen()) $a_filter[] = $p_model::getListFilter('unit',Modul::getUnit());
	if(Akademik::isDosen()) $a_filter[] = $p_model::getListFilter('pembimbing',Modul::getUserName());
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	// $a_filtercombo = array();
	// $a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	
	// filter tree
	$a_filtertree = array();
	$a_filtertree['unit'] = array('label' => 'Prodi', 'data' => mCombo::unitTree($conn,true));
	$a_filtertree['pembimbing'] = array('label' => 'Pembimbing Skripsi', 'data' => $p_model::pembimbingSkripsi());
	$a_filtertree['lama'] = array('label' => 'Lama Skripsi', 'data' => $p_model::lamaSkripsi());
	
	require_once(Route::getViewPath('inc_list'));
?>
