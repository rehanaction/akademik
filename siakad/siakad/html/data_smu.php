<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	 $c_insert = $a_auth['caninsert'];
	 $c_update = $a_auth['canupdate'];
	 $c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('smu'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(!empty($r_key) and $c_update)
		$c_edit = true;
	else if(empty($r_key) and $c_insert)
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data SMU';
	$p_tbwidth = 640;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mSmu;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$kota=$p_model::getKota($conn);
	
	$a_input = array();
	$a_input[] = array('kolom' => 'namasmu', 'label' => 'SMU', 'size' => 50, 'maxlength' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat', 'size' => 4, 'maxlength' => 50, 'notnull' => true, 'type'=>'A');
	$a_input[] = array('kolom' => 'telpsmu', 'label' => 'Telp', 'size' => 15, 'maxlength' => 13, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'notnull' => true, 'type'=>'S', 'option' => $kota);

	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}

	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
