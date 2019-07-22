<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = false;
	$c_update = $a_auth['canupdate'];
	$c_delete = false;
	
	// include
	require_once(Route::getModelPath('setting'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	//$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_key = 'isopname';
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Kunci Opname';
	$p_tbwidth = 400;
	$p_aktivitas = 'kunci opname';
	$p_listpage = Route::getListPage();
	
	$p_model = mSetting;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_isopname = array('1' => 'Buka', 'O' => 'Tutup');
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nilai', 'label' => 'Kunci Opname ?', 'type' => 'R', 'option' => $a_isopname, 'readonly' => false);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	require_once(Route::getViewPath('inc_data'));
?>
