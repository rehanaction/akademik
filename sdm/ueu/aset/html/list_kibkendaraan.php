<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	$_SESSION['SERI'] = 'list_kibkendaraan';
	
	// include
	require_once(Route::getModelPath('kibkendaraan'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Daftar KIB Kendaraan';
	$p_tbwidth = 900;
	$p_aktivitas = 'KIB Kendaraan';
	$p_detailpage = Route::navAddress('data_seri');
	
	$p_model = mKIBKendaraan;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idbarang1', 'label' => 'ID. Barang', 'width' => 110, 'align' => 'center', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'noseri', 'label' => 'No. Seri', 'width' => 60, 'align' => 'center', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'namabarang', 'label' => 'Nama Barang', 'align' => 'left');
	$a_kolom[] = array('kolom' => 'nopol', 'label' => 'No. Polisi', 'width' => 80);	
	$a_kolom[] = array('kolom' => 'nostnk', 'label' => 'No. STNK', 'width' => 100);
	$a_kolom[] = array('kolom' => 'merk', 'label' => 'Merk', 'width' => 100);
	$a_kolom[] = array('kolom' => 'tipe', 'label' => 'Tipe', 'width' => 75);
	$a_kolom[] = array('kolom' => 'warna', 'label' => 'Warna', 'width' => 75, 'align' => 'center');
	
	$p_colnum = count($a_kolom)+1;
	
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
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	require_once(Route::getViewPath('inc_list'));
?>
