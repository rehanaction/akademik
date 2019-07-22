<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kota'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_propinsi = Modul::setRequest($_POST['propinsi'],'PROPINSI');
	
	// combo
	$l_propinsi = uCombo::propinsi($conn,$r_propinsi,'propinsi','onchange="goSubmit()"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodekota', 'label' => 'Kode', 'size' => 4, 'maxlength' => 4, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'namakota', 'label' => 'Nama Kota/Kabupaten', 'size' => 40, 'maxlength' => 50, 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Kota';
	$p_tbwidth = 400;
	$p_aktivitas = 'WILAYAH';
	
	$p_model = mKota;
	$p_key = $p_model::key;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan propinsi
		$a_kolom[] = array('kolom' => 'kodepropinsi', 'value' => $r_propinsi);
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
		// buang lagi propinsi
		array_pop($a_kolom);
		
		//list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
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
	
	// mendapatkan data
	if(!empty($r_propinsi)) $a_filter[] = $p_model::getListFilter('propinsi',$r_propinsi);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Propinsi', 'combo' => $l_propinsi);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
