<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('ijinseminar'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Data Ijin Seminar';
	$p_tbwidth = 800;
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mIjinSeminar;
	
	// struktur view
	$a_periode = mCombo::periode($conn);
	$a_pengaju = array('M' =>'Mahasiswa' ,'P' =>'Pegawai');
	
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'Nomor');
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode');
	//$a_kolom[] = array('kolom' => 'idijinseminar', 'label' => 'Ijin Seminar');
	$a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Keterangan');
	$a_kolom[] = array('kolom' => 'pengaju', 'label' => 'Pengaju');
	/*
	$a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Keterangan');
	$a_kolom[] = array('kolom' => 'tglawaldaftar', 'label' => 'Tgl Awal', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'tglakhirdaftar', 'label' => 'Tgl Akhir', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'nippengajuseminar', 'label' => 'Pengaju Seminar');
	$a_kolom[] = array('kolom' => 'kodejenisseminar', 'label' => 'Kode Jenis Seminar');
	$a_kolom[] = array('kolom' => 'tarifseminar', 'label' => 'Tarif Seminar');
	*/
	
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
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>
