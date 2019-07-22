<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$p_printpagedet = "rep_ukt";
	
	$p_detailpage = "data_ukt";
	// include
	require_once(Route::getModelPath('ukt'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periodebayar'],'PERIODE');
	$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT');
	
	$l_periode = uCombo::periode($conn,$r_periode,'periodebayar','onchange="goSubmit()"',true);
	$l_unit = uCombo::unit($conn,$r_unit,'kodeunit','onchange="goSubmit()"',true);
		
        
	// properti halaman
	$p_title = 'Daftar Tarif UKT';
	$p_tbwidth = '960';
	$p_aktivitas = 'Master';
	

	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Kode Unit','align'=>'center');
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode','align'=>'center');
	$a_kolom[] = array('kolom' => 'namakategoriukt', 'label' => 'Kode Kategori UKT','align'=>'center');
	$a_kolom[] = array('kolom' => 'nilaitarif', 'label' => 'Tarif','align'=>'center');
	$a_kolom[] = array('kolom' => 'u.keterangan', 'label' => 'Keterangan','align'=>'center');
	
	$p_model = mUkt;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
		// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	// mendapatkan data
	//if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('kodeunit',$r_unit);
 
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'kolom' => 'periodebayar','combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	
	require_once(Route::getViewPath('v_list_ukt'));
?>
