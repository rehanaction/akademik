<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('yudisium'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Peserta Yudisium';
	$p_tbwidth = 500;
	$p_aktivitas = 'WISUDA';
	$p_listpage = Route::getListPage();
	
	$p_model = mYudisium;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nim', 'label' => 'NIM', 'maxlength' => 10, 'size' => 15, 'readonly' => true);
	$a_input[] = array('kolom' => 'nama', 'label' => 'Nama', 'maxlength' => 50, 'size' => 30, 'readonly' => true);
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Prodi', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'readonly' => true);
	$a_input[] = array('kolom' => 'periodewisuda', 'label' => 'Periode Wisuda', 'type' => 'S', 'option' => mCombo::periodeWisuda($conn,false));
	$a_input[] = array('kolom' => 'noijasah', 'label' => 'No Ijazah Institut', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'notranskrip', 'label' => 'No Ijazah Fakultas', 'maxlength' => 50, 'size' => 30);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>