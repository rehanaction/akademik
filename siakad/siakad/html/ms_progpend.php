<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('progpend'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'programpend', 'label' => 'Program', 'size' => 5, 'maxlength' => 5, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'lamastudi', 'label' => 'Maksimal Studi (smt)', 'size' => 2, 'maxlength' => 2);
	$a_kolom[] = array('kolom' => 'maxcuti', 'label' => 'Maksimal Cuti', 'size' => 2, 'maxlength' => 2);
	$a_kolom[] = array('kolom' => 'kodenim', 'label' => 'Kode NIM', 'size' => 1, 'maxlength' => 1);
	
	// properti halaman
	$p_title = 'Daftar Program Pendidikan';
	$p_tbwidth = 400;
	$p_aktivitas = 'UNIT';
	
	$p_model = mProgrampendidikan;
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
