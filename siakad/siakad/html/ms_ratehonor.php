<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = true;
	$c_edit = $a_auth['canupdate'];
	$c_delete = true;
	
	// include
	require_once(Route::getModelPath('ratehonor'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mRateHonor;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kdjnshonor', 'label' => 'Kode Honor', 'size' => 3, 'maxlength' => 3, 'notnull' => true);
	//$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'Basis', 'type' => 'S', 'option' => $p_model::sistemKuliah($conn));
	
	$a_kolom[] = array('kolom' => 'nmjnshonor', 'label' => 'Nama Honor', 'size' => 25, 'maxlength' => 50);
	$a_kolom[] = array('kolom' => 'rate', 'label' => 'Rate', 'size' => 10, 'maxlength' => 10, 'notnull' => true);
	
	
	// properti halaman
	$p_title = 'Data Rate Honor';
	$p_tbwidth = 700;
	$p_aktivitas = 'UNIT';
	
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

	

