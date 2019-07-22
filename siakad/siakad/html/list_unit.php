<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Data Unit';
	$p_tbwidth = 900;
	$p_aktivitas = 'UNIT';
	
	$p_model = mUnit;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'u.kodeunit', 'label' => 'Kode Unit');
	$a_kolom[] = array('kolom' => 'u.namaunit', 'label' => 'Nama Unit');
	$a_kolom[] = array('kolom' => 'up.namaunit', 'alias' => 'namaparent', 'label' => 'Induk');
	$a_kolom[] = array('kolom' => 'kodeurutan', 'label' => 'Kode Urutan');
	$a_kolom[] = array('kolom' => 'level', 'label' => 'Level', 'type' => 'S', 'option' => $p_model::namaLevel()); // type S nggak baik buat sort dan filter
	$a_kolom[] = array('kolom' => 'p.nama', 'label' => 'Ketua');
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	require_once($conf['view_dir'].'inc_list.php');
?>
