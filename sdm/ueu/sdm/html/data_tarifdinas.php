<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastdinas'));
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	$r_key = CStr::removeSpecial($_REQUEST['key']);
					
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Tarif Perjalanan Dinas';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$p_model = mMastDinas;
	$p_dbtable = "ms_tarifperjalanan";
	$p_key = "idtarifrate";
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'jnsrate', 'label' => 'Jenis Rate', 'type' => 'S', 'option' => $p_model::jenisRate(), 'notnull' => true);
	$a_input[] = array('kolom' => 'idjabatan', 'label' => 'Jabatan', 'type' => 'S', 'option' => mMastKepegawaian::Jabatan($conn), 'notnull' => true);	
	$a_input[] = array('kolom' => 'idrate', 'label' => 'Tipe Rate', 'type' => 'S', 'option' => $p_model::getCTipeRate($conn), 'notnull' => true);
	$a_input[] = array('kolom' => 'tarifrate', 'label' => 'Tarif', 'maxlength' => 14, 'size' => 14, 'type' => 'N');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$conn->BeginTrans();
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,'',true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	require_once(Route::getViewPath('inc_data'));
?>