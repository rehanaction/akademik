<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jenispegawai'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idjenispegawai', 'label' => 'Kode<br>Jenis<br>Pegawai', 'width'=>'5%');
	$a_kolom[] = array('kolom' => 'b.tipepeg', 'label' => 'Tipe<br>Pegawai', 'width'=>'150');
	$a_kolom[] = array('kolom' => 'jenispegawai', 'label' => 'Jenis Pegawai','notnull' => true);
	$a_kolom[] = array('kolom' => 'isaktif', 'label' => 'Aktif ?','notnull' => true, 'width'=>'5%');
	$a_kolom[] = array('kolom' => 'isnaikpangkat', 'label' => 'Naik<br>Pangkat ?', 'width'=>'7%');
	$a_kolom[] = array('kolom' => 'c.namarole', 'label' => 'Kode Role', 'width'=>'20%');

	// properti halaman
	$p_title = 'Data Jenis Pegawai';
	$p_tbwidth = 900;
	$p_aktivitas = 'Master Data';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mJenispegawai;
	$p_key = $p_model::key;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
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