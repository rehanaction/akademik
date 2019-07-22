<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pendaftarseminar'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Data Pendaftar Mahasiswa';
	$p_tbwidth = 950;
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPemdaftarSeminar;
	
	// struktur view
	$a_periode = mCombo::periode($conn);
	
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No');
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'rfid', 'label' => 'RFID');
	$a_kolom[] = array('kolom' => 'nopendaftar', 'label' => 'Nomor Pendaftaran');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'sex', 'label' => 'Jenis Kelamin');
	$a_kolom[] = array('kolom' => 'noktp', 'label' => 'No KTP');
	$a_kolom[] = array('kolom' => 'telp', 'label' => 'Telp');
	$a_kolom[] = array('kolom' => 'email', 'label' => 'Mail');
	
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
	if($r_act == 'copy' and $c_edit) {
		list($p_posterr,$p_postmsg) = $p_model::updateRFIDMahasiswa($conn);
	}
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

	$a_filter[] = $p_model::getListFilter('nim','');
	
	// mendapatkan data
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);

	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	if($c_edit) {
		$a_salin = true; // hanya untuk mengaktifkan function js goSalin()
		$a_filtercombo[] = array('label' => 'Update RFID', 'combo' => '<input type="button" value="Update" class="ControlStyle" onClick="goSalin()">'); // meskipun bukan salin, hehehe
	}
	
	require_once(Route::getViewPath('inc_list'));
?>