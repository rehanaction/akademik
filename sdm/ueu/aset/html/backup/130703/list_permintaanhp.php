<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('transhp'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_tok = 'K';
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:250px;"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idtranshp', 'label' => 'ID.', 'width' => 40);
	$a_kolom[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'width' => 100, 'type' => 'D');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit');
	//$a_kolom[] = array('kolom' => 'tglpembukuan', 'label' => 'Tgl. Pembukuan', 'width' => 100, 'type' => 'D');
	//$a_kolom[] = array('kolom' => 'namapegawai', 'label' => 'Pegawai', 'width' => 150);
	$a_kolom[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'cekval' => '1', 'align' => 'center', 'width' => 60, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'tgltransaksi', 'label' => 'Tgl. Pengeluaran', 'width' => 100, 'type' => 'D', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'status', 'label' => 'Status', 'align' => 'center', 'width' => 50, 'nosearch' => true);
	
	// properti halaman
	$p_title = 'Daftar Permintaan Habis Pakai';
	$p_tbwidth = 750;
	$p_aktivitas = 'permintaan habis pakai';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mTransHP;
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		$a_mdata = $p_model::getMData($conn, $r_key);
		
		if($a_mdata['isverify'] == '1'){
		    $p_posterr = true;
		    $p_postmsg = 'Data ini sudah diverifikasi dan tidak dapat dihapus ';
		}else
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
	if(!empty($r_tok)) $a_filter[] = $p_model::getListFilter('tok',$r_tok);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);

    require_once(Route::getViewPath('inc_list'));
?>
