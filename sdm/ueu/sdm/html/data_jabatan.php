<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Jenis Jabatan';
	$p_tbwidth = 600;
	$p_aktivitas = 'UNIT';
	$p_dbtable = 'ms_jabatan';
	$p_key = 'idjabatan';
	$p_listpage = Route::getListPage();
	
	$p_model = mMastKepegawaian;
	$a_pangkat = $p_model::aPangkat($conn);
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idjabatan', 'label' => 'Kode', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'namajabatan', 'label' => 'Nama Jabatan', 'maxlength' => 100, 'size' => 70, 'notnull' => true);
	$a_input[] = array('kolom' => 'level', 'label' => 'Level', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'kodeeselon', 'label' => 'Eselon', 'type' => 'S', 'option' => $p_model::aEselon($conn));
	$a_input[] = array('kolom' => 'pangkatmin', 'label' => 'Pangkat Min', 'type' => 'S', 'option' => $a_pangkat, 'empty' => true);
	$a_input[] = array('kolom' => 'pangkatmax', 'label' => 'Pangkat Max', 'type' => 'S', 'option' => $a_pangkat, 'empty' => true);
	$a_input[] = array('kolom' => 'bebansks', 'label' => 'Beban SKS', 'maxlength' => 4, 'size' => 5, 'type' => 'N');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	require_once(Route::getViewPath('inc_data'));
?>
