<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_supplier');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('supplier'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Supplier';
	$p_tbwidth = 500;
	$p_aktivitas = 'Supplier';
	$p_listpage = Route::getListPage();
	
	$p_model = mSupplier;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	//$a_input[] = array('kolom' => 'idsupplier', 'label' => 'ID Supplier', 'maxlength' => 10, 'size' => 3, 'notnull' => true);
	$a_input[] = array('kolom' => 'namasupplier', 'label' => 'Nama Supplier', 'maxlength' => 45, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'idjenissupplier', 'label' => 'Jenis Supplier', 'type' => 'S', 'option' => mCombo::jenissupplier($conn,false), 'add' => 'style="width:200px"', 'empty' => true);
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'namacp', 'label' => 'Contact Person', 'maxlength' => 16, 'size' => 16);
	$a_input[] = array('kolom' => 'kota', 'label' => 'Kota', 'maxlength' => 45, 'size' => 30);
	$a_input[] = array('kolom' => 'provinsi', 'label' => 'Provinsi', 'maxlength' => 45, 'size' => 30);
	$a_input[] = array('kolom' => 'kodepos', 'label' => 'Kodepos', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'notlp', 'label' => 'No. Tlp', 'maxlength' => 16, 'size' => 16);
	$a_input[] = array('kolom' => 'nohp', 'label' => 'No. Hp', 'maxlength' => 16, 'size' => 16);
	$a_input[] = array('kolom' => 'npwp', 'label' => 'No. NPWP', 'maxlength' => 16, 'size' => 16);
	$a_input[] = array('kolom' => 'siup', 'label' => 'No. SIUP', 'maxlength' => 16, 'size' => 16);
	$a_input[] = array('kolom' => 'nofax', 'label' => 'No. Fax', 'maxlength' => 16, 'size' => 16);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 45, 'size' => 30);
	$a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
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
