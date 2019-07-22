<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('penghapusan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_jenispenghapusan = Modul::setRequest($_POST['jenispenghapusan'],'JENISPENGHAPUSAN');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:250px;"',false);
	$l_jenispenghapusan = uCombo::jenispenghapusan($conn,$r_jenispenghapusan,'jenispenghapusan','onchange="goSubmit()" style="width:250px;"',true);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idpenghapusan', 'label' => 'ID.', 'width' => 40);
	$a_kolom[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'width' => 80, 'type' => 'D', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit');
	/*$a_kolom[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti', 'width' => 150);*/
	$a_kolom[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'cekval' => '1', 'align' => 'center', 'width' => 60, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'isok1', 'label' => 'Disetujui ?', 'cekval' => '1', 'align' => 'center', 'width' => 60, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'jenispenghapusan', 'label' => 'Jenis Penghapusan', 'width' => 150);
	$a_kolom[] = array('kolom' => 'tglpenghapusan', 'label' => 'Tgl. Penghapusan', 'width' => 100, 'type' => 'D', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'status', 'label' => 'Status', 'align' => 'center', 'width' => 50, 'nosearch' => true);
	
	// properti halaman
	$p_title = 'Daftar Penghapusan Aset';
	$p_tbwidth = 900;
	$p_aktivitas = 'Penghapusan';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPenghapusan;
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
	if(!empty($r_jenispenghapusan)) $a_filter[] = $p_model::getListFilter('jenispenghapusan',$r_jenispenghapusan);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Jenis', 'combo' => $l_jenispenghapusan);

    require_once(Route::getViewPath('inc_list'));
?>
