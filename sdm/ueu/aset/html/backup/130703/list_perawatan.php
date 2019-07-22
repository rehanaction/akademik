<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('rawat'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	//$r_jenisrawat = Modul::setRequest($_POST['jenisrawat'],'JENISRAWAT');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:250px;"',false);
	//$l_jenisrawat = uCombo::jenisrawat($conn,$r_jenisrawat,'jenisrawat','onchange="goSubmit()" style="width:250px;"',true);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idrawat', 'label' => 'ID.', 'width' => 40);
	$a_kolom[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'nosearch' => true, 'type' => 'D', 'width' => 100);
	$a_kolom[] = array('kolom' => 'unit', 'label' => 'Unit', 'nosearch' => true);
	//$a_kolom[] = array('kolom' => 'jenisrawat', 'label' => 'Jenis Perawatan', 'nosearch' => true, 'width' => 150);
	$a_kolom[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'align' => 'center', 'nosearch' => true, 'cekval' => '1', 'width' => 60);
	$a_kolom[] = array('kolom' => 'isok1', 'label' => 'Disetujui ?', 'align' => 'center', 'nosearch' => true, 'cekval' => '1', 'width' => 60);
	$a_kolom[] = array('kolom' => 'tglrawat', 'label' => 'Tgl. Perawatan', 'nosearch' => true, 'type' => 'D', 'width' => 100);
	$a_kolom[] = array('kolom' => 'status', 'label' => 'Status', 'align' => 'center', 'nosearch' => true, 'width' => 40);
	
	// properti halaman
	$p_title = 'Daftar Perawatan Aset';
	$p_tbwidth = 800;
	$p_aktivitas = 'perawatan';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mRawat;
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
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	//if(!empty($r_jenisrawat)) $a_filter[] = $p_model::getListFilter('jenisrawat',$r_jenisrawat);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	//$a_filtercombo[] = array('label' => 'Jenis', 'combo' => $l_jenisrawat);

    require_once(Route::getViewPath('inc_list'));
?>
