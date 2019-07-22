<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('historyupload'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Riwayat Upload Pembayaran';
	$p_aktivitas = 'SUBMIT';
	$p_tbwidth = '700';
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label'=>'No', 'width'=>'3%');
	$a_kolom[] = array('kolom' => 'uploadtime', 'label'=>'Waktu Upload', 'type'=>'DT');
	$a_kolom[] = array('kolom' => 'user', 'label'=>'User');
	$a_kolom[] = array('kolom' => 'jumlahtransaksi', 'label'=>'Jml Trans', 'align'=>'center');
	$a_kolom[] = array('kolom' => 'jumlahbayar', 'label'=>'Total Bayar', 'type'=>'N', 'align'=>'right');
	$a_kolom[] = array('kolom' => 'berhasil', 'label'=>'Berhasil', 'type'=>'N', 'align'=>'center');
	$a_kolom[] = array('kolom' => 'gagal', 'label'=>'Gagal', 'type'=>'N', 'align'=>'center');
	
	$p_model = mHistoryUpload;
	$p_colnum = count($a_kolom);
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);

	$p_lastpage = Page::getLastPage();
	
	require_once($conf['view_dir'].'inc_list.php');
?>