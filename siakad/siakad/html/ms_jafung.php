<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jafung'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idjfungsional', 'label' => 'ID Jafung');
	$a_kolom[] = array('kolom' => 'jabatanfungsional', 'label' => 'Nama Jabatan','notnull' => true);
	$a_kolom[] = array('kolom' => 'pangkat', 'label' => 'Pangkat','notnull' => true);
	$a_kolom[] = array('kolom' => 'golongan', 'label' => 'Golongan','notnull' => true);
	$a_kolom[] = array('kolom' => 'angkakredit', 'label' => 'Angka Kredit','notnull' => true);
	$a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Keterangan','notnull' => true);
	$a_kolom[] = array('kolom' => 'ratehonor', 'label' => 'Rate Honor','notnull' => true);


	// properti halaman
	$p_title = 'Daftar Jabatan Fungsional';
	$p_tbwidth = "100%";
	$p_aktivitas = 'Data Jabatan Fungsional';
	
	$p_model = mJafung;
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