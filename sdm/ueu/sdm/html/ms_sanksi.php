<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastaktifitas'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mMastAktifitas;
	$p_dbtable = 'lv_sanksi';
	$p_key = 'jenissanksi';
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'jenissanksi', 'label' => 'Kode Sanksi', 'size' => 3, 'maxlength' => 3, 'align' => 'center', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Nama Jenis Sanksi', 'size' => 40, 'maxlength' => 255, 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Jenis Sanksi';
	$p_tbwidth = 500;
	$p_aktivitas = 'BIODATA';
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST,$p_dbtable);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'keterangan';
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,'','',$p_dbtable);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
