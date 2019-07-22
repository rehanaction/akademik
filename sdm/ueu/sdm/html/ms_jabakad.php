<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mMastKepegawaian;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idjfungsional', 'label' => 'Kode', 'size' => 5, 'maxlength' => 5, 'align' => 'center', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'jabatanfungsional', 'label' => 'Nama Jabatan','size' => 30, 'maxlength' => 100, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'kodefungsional', 'label' => 'Singkatan', 'size' => 5, 'maxlength' => 5, 'align' => 'center', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'angkakredit', 'label' => 'Nilai KUM','size' => 5, 'maxlength' => 6, 'notnull' => true, 'align' => 'right');
	$a_kolom[] = array('kolom' => 'irkd', 'label' => 'Nilai IRKD','size' => 2, 'maxlength' => 2, 'notnull' => true, 'align' => 'right');
	$a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Keterangan','size' => 30, 'maxlength' => 255, 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Jabatan Akademik';
	$p_tbwidth = 750;
	$p_aktivitas = 'BIODATA';
	$p_dbtable = 'ms_fungsional';
	$p_key = 'idjfungsional';
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
	if(empty($r_sort)) $r_sort = 'idjfungsional';
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter,'',$p_dbtable);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
