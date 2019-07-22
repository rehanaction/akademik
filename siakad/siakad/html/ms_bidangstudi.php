<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('bidangstudi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodebs', 'label' => 'Kode', 'size' => 5, 'maxlength' => 5, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'namabs', 'label' => 'Nama', 'size' => 30, 'maxlength' => 50);
	$a_kolom[] = array('kolom' => 'namabsen', 'label' => 'Nama (EN)', 'size' => 30, 'maxlength' => 50);
	
	// properti halaman
	$p_title = 'Bidang Studi';
	$p_tbwidth = 550;
	$p_aktivitas = 'UNIT';
	
	$p_model = mBidangStudi;
	$p_key = $p_model::key;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan unit
		$a_kolom[] = array('kolom' => 'kodeunit', 'value' => $r_unit);
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
		// buang lagi unit
		array_pop($a_kolom);
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
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Program Studi', 'combo' => $l_unit);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>