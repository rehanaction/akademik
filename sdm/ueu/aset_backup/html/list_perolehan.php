<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = false;//$a_auth['candelete'];

	$_SESSION['PEROLEHAN'] = 'list_perolehan';
	
	// include
	require_once(Route::getModelPath('perolehanheader'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
	$r_cfilter = Modul::setRequest($_POST['cfilter'],'CFILTER');

	if(empty($r_tahun)) $r_tahun = date("Y");
	if(empty($r_bulan)) $r_bulan = date("m");

	// combo
	$lr = Modul::getLeftRight();
	if($lr['LEFT'] == '1')
    	$l_unit = uCombo::unitAuto($conn,$r_unit,'unit','onchange="goSubmit()"');
	else
    	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:250px;"',false);
	$l_tahun = uCombo::tahun($conn,$r_tahun,'tahun','onchange="goSubmit()"',false);
	$l_bulan = uCombo::bulan($conn,$r_bulan,'bulan','onchange="goSubmit()"',false);
	
	$r_role = Modul::getRole();
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idperolehanheader', 'label' => 'ID.', 'width' => 40);
	$a_kolom[] = array('kolom' => 'tglperolehan', 'label' => 'Tgl. Perolehan', 'width' => 80, 'type' => 'D');
	$a_kolom[] = array('kolom' => 'unit', 'label' => 'Unit', 'nosearch' => true);
	if($r_role == 'pk')
	    $a_kolom[] = array('kolom' => 'listbarang', 'label' => 'Barang', 'width' => 150);
    else
    	$a_kolom[] = array('kolom' => 'namasupplier', 'label' => 'Supplier', 'width' => 150);

	$a_kolom[] = array('kolom' => 'nopo', 'label' => 'No. PO', 'width' => 100);
	$a_kolom[] = array('kolom' => 'tglpo', 'label' => 'Tgl. PO', 'width' => 80, 'type' => 'D');
	$a_kolom[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti', 'width' => 100);
	$a_kolom[] = array('kolom' => 'tglbukti', 'label' => 'Tgl. Bukti', 'width' => 80, 'type' => 'D');
	//$a_kolom[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'align' => 'center', 'nosearch' => true, 'cekval' => '1', 'width' => 40);
	
	// properti halaman
	$p_title = 'Daftar Perolehan Aset';
	$p_tbwidth = 950;
	$p_aktivitas = 'Perolehan';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPerolehanHeader;
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if($r_cfilter != 'nopo') 
	    if(!empty($r_tahun) or !empty($r_bulan)) $a_filter[] = $p_model::getListFilter('periode','',$r_tahun,$r_bulan);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	if($r_cfilter != 'nopo') 
    	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_bulan.'&nbsp;&nbsp;&nbsp;'.$l_tahun);
	
    $lockmsg = Aset::isLock($conn, Aset::setTglToPeriode($r_tahun.'-'.$r_bulan));
	if(!empty($lockmsg)){
		$c_delete = false;

        $p_posterr = true;
        $p_postmsg = $lockmsg;
	}

    require_once(Route::getViewPath('inc_list'));
?>
