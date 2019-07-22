<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	
	// include
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_kurikulum = Modul::setRequest($_POST['kurikulum'],'KURIKULUM');
	
	// combo
	$l_kurikulum = uCombo::kurikulum($conn,$r_kurikulum,'kurikulum','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Data Mata Kuliah';
	$p_tbwidth = 730;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	$p_model = mPegawai;
	
	// struktur view
	$a_kolom = array();
	
	$a_kolom[] = array('kolom' => 'nodosen', 'label' => 'No. Dosen');
	$a_kolom[] = array('kolom' => 'namadosen', 'label' => 'Nama Dosen');
	$a_kolom[] = array('kolom' => 'norekeninghonor', 'label' => 'No Rekening');
	$a_kolom[] = array('kolom' => 'namajnsrate', 'label' => 'Jenis Rate');
	$a_kolom[] = array('kolom' => 'nominal', 'label' => 'Rate');
	$a_kolom[] = array('kolom' => 'pph', 'label' => 'PPH');
	$a_kolom[] = array('kolom' => 'biatrans', 'label' => 'Biaya Transfer');
	$a_kolom[] = array('kolom' => 'isvalid', 'label' => 'Valid ?');
	
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
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_kurikulum)) $a_filter[] = $p_model::getListFilter('thnkurikulum',$r_kurikulum);
	
	$sql=$p_model::getQueryRate($r_sort,$a_filter);
	$a_data = $p_model::getPagerData($conn_sdm,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Kurikulum', 'combo' => $l_kurikulum);
	
	// filter tree
	$a_filtertree = array();
	$a_filtertree['thnkurikulum'] = array('label' => 'Kurikulum', 'data' => mCombo::kurikulum($conn));
	$a_filtertree['kodejenis'] = array('label' => 'Jenis', 'data' => mMataKuliah::jenisMataKuliah($conn));
	
	require_once(Route::getViewPath('inc_list'));
?>
