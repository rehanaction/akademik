<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	//$c_edit = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	
	$p_detailpage = "data_pembayaranallfrm";
	// include
	require_once(Route::getModelPath('pembayaranfrm'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periodedaftar'],'PERIODE DAFTAR');
	
	// combo
	$l_periode = uCombo::periodedaftar($conn,$r_periode,'periodedaftar','onchange="goSubmit()"',true);
		
        
	// properti halaman
	$p_title = 'Daftar Pembelian Token';
	$p_tbwidth = '1000';
	$p_aktivitas = 'Master';
	

	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idpembayaranfrm', 'label' => 'ID Bayar', 'width'=>'6%','align'=>'center');
	$a_kolom[] = array('kolom' => 'tglbayar', 'label' => 'Tgl Bayar','align'=>'center');
	$a_kolom[] = array('kolom' => 'jumlahbayar', 'label' => 'Jumlah','align'=>'right','type'=>'N');
	$a_kolom[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur');
	$a_kolom[] = array('kolom' => 'namasistem', 'label' => 'Sistem Kuliah');
	$a_kolom[] = array('kolom' => 'namagelombang', 'label' => 'Gelombang');
	$a_kolom[] = array('kolom' => 'programpend', 'label' => 'Jenjang');
	$a_kolom[] = array('kolom' => 'jumlahpilihan', 'label' => 'Pilihan');
	$a_kolom[] = array('kolom' => 'nip', 'label' => 'Petugas','align'=>'center');
	$a_kolom[] = array('kolom' => 'refno', 'label' => 'No. Ref.','align'=>'center');
	$a_kolom[] = array('kolom' => 'notoken', 'label' => 'Token','align'=>'center');
	$a_kolom[] = array('kolom' => 'ish2h', 'label' => 'H2H?', 'type' => 'C', 'option' => array('1' => ''), 'width'=>'3%','align'=>'center');
	$a_kolom[] = array('kolom' => 'flagbatal', 'label' => 'Batal?', 'type' => 'C', 'option' => array('1' => ''), 'width'=>'4%','align'=>'center');
	
	$p_model = mPembayaranfrm;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
		// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periodedaftar',$r_periode);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'kolom' => 'periodebayar','combo' => $l_periode);
	
	require_once(Route::getViewPath('inc_list'));
?>
