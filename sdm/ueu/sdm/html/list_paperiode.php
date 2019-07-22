<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
		
	$p_model = mPa;
			
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeperiode', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'namaperiode', 'label' => 'Nama Periode');
	$a_kolom[] = array('kolom' => 'periodebobot', 'label' => 'Periode Bobot');
	$a_kolom[] = array('kolom' => 'tglmulai', 'label' => 'Tgl. Mulai', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'tglakhir', 'label' => 'Tgl. Selesai', 'type' => 'D');
	
	// properti halaman
	$p_title = 'Daftar Periode Penilaian';
	$p_tbwidth = 700;
	$p_aktivitas = 'NILAI';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = "pa_periode";
	$p_key = "kodeperiode";
	
	$p_colnum = count($a_kolom)+2;
	
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
	if (empty($r_sort)) $r_sort = 'tglakhir desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listQueryPeriodePA();
					
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	require_once(Route::getViewPath('inc_list'));
?>