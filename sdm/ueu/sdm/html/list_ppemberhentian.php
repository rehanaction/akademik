<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_edit = $a_auth['canupdate'];
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('pekerjaan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel esensial
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	//cek ada tidak data
	$cek = mPekerjaan::cekDataPemberhentian($conn,$r_key);
	
	if($c_kepeg){
		if($cek != 1)
			$c_insert = $a_auth['caninsert'];
		
		$c_delete = $a_auth['candelete'];
	}
		
	// properti halaman
	$p_title = 'Data Pemberhentian';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORY';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPekerjaan;
	$p_key = 'idpegawai';
	$p_dbtable = 'pe_pensiun';
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'tmtpensiun', 'label' => 'Tgl. Pensiun', 'type' => 'D', 'align' => 'center', 'width' => '150px');
	$a_kolom[] = array('kolom' => 'statusaktif', 'label' => 'Jenis Pensiun');
	$a_kolom[] = array('kolom' => 'masakerja', 'label' => 'Masa Kerja', 'align' => 'center','width' => '200px');
	$a_kolom[] = array('kolom' => 'skpensiun', 'label' => 'SK Pensiun', 'maxlength' => 50, 'size' => 30);

	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$where = 'idpegawai';
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$where,'','filepensiun');
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'tmtpensiun desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listQueryPemberhentian($r_key);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
		
	require_once(Route::getViewPath('inc_listajax'));
?>
