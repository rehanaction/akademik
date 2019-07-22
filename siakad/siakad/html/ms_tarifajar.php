<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('tarifajar'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mTarifajar;
	$status=array('0'=>'Offline','-1'=>'Online');
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'Sistem Kuliah', 'type' => 'S', 'option' => $p_model::sistemKuliah($conn));
	$a_kolom[] = array('kolom' => 'nohari', 'label' => 'Hari', 'type' => 'S', 'option' => Date::arrayDay());
	$a_kolom[] = array('kolom' => 'jeniskuliah', 'label' => 'Jenis Pertemuan', 'type' => 'S', 'option' => Mkuliah::jenisKuliah($conn));
	$a_kolom[] = array('kolom' => 'isonline', 'label' => 'Online?', 'type' => 'R', 'option' => $status);	
	$a_kolom[] = array('kolom' => 'nkelipatan', 'label' => 'Kelipatan Gaji', 'size' => 4, 'maxlength' => 4, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'nkelipatansks', 'label' => 'Kelipatan SKS', 'size' => 4, 'maxlength' => 4, 'notnull' => true);
	
	
	// properti halaman
	$p_title = 'Setting Tarif Mengajar';
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

	

