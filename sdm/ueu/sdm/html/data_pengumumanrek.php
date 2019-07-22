<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastpelengkap'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pengumuman Rekrutmen';
	$p_tbwidth = 600;
	$p_aktivitas = 'NEWS';
	$p_dbtable = "pe_pengumumanrek";
	$p_key = "idpengumumanrek";
	$p_listpage = Route::getListPage();
	$ismce = true;
	
	$p_model = mMastPelengkap;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tanggal Posting', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tanggal Kadaluarsa', 'type' => 'D');
	$a_input[] = array('kolom' => 'judulpengumuman', 'label' => 'Judul Pengumuman', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'isipengumuman', 'label' => 'Isi Pengumuman', 'type' => 'M');
	$a_input[] = array('kolom' => 'filepengumumanrek', 'label' => 'File Pengumuman', 'type' => 'U', 'uptype' => 'filepengumumanrek', 'size' => 40);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key,'','filepengumumanrek');
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,$p_dbtable,'filepengumumanrek',$p_key);
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	require_once(Route::getViewPath('inc_data'));
?>
