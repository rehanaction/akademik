<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('histori'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'h.kodehistori', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'namahistori', 'label' => 'Aksi');
	$a_kolom[] = array('kolom' => 'dataasal', 'label' => 'Data Asal');
	$a_kolom[] = array('kolom' => 'dataubah', 'label' => 'Data Ubah');
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode');
	$a_kolom[] = array('kolom' => 'kodemk', 'label' => 'Kode MK');
	$a_kolom[] = array('kolom' => 't_updatetime', 'label' => 'Waktu', 'type' => 'D');
	$a_kolom[] = array('kolom' => 't_updateuser', 'label' => 'User');
	$a_kolom[] = array('kolom' => 't_updateip', 'label' => 'Alamat IP');
	
	// properti halaman
	$p_title = 'Daftar Histori Perubahan Nilai';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORI';
	
	$p_model = mHistoriNilai;
	$p_colnum = count($a_kolom);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>