<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jeniskuliah'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idjeniskuliah', 'label' => 'Kode jenis', 'size' => 5, 'maxlength' => 5, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'namajeniskuliah', 'label' => 'Nama Jenis Kuliah', 'size' => 30, 'maxlength' => 30, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'iskelompok', 'label' => 'Termasuk Kelompok ?', 'type' => 'R', 'option' => mJenisKuliah::kelompok());
	
	// properti halaman
	$p_title = 'Jenis Perkuliahan';
	$p_tbwidth = 500;
	$p_aktivitas = 'WILAYAH';
	
	$p_model = mJenisKuliah;
	$p_key = $p_model::key;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
