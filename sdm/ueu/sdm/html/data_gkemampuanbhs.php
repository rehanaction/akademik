<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_readlist = true;		
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('pengembangan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai())
		$r_self = 1;
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_edit = $a_auth['canupdate'];
		$c_delete = $a_auth['candelete'];
	}
		
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
			
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	// properti halaman
	$p_title = 'Data Kemampuan Bahasa';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mPengembangan;
	$p_dbtable = "pe_kemampuanbhs";
	$where = 'nourutkbhs';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tahunkemampuan', 'label' => 'Tahun', 'maxlength' => 4, 'size' => 4, 'notnull' => true);
	$a_input[] = array('kolom' => 'namalembaga', 'label' => 'Nama Lembaga', 'maxlength' => 100, 'size' => 70);
	$a_input[] = array('kolom' => 'kemampuandengar', 'label' => 'Kemampuan Mendengar', 'type' => 'N', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'kemampuanbicara', 'label' => 'Kemampuan Bicara', 'type' => 'N', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'kemampuantulisan', 'label' => 'Kemampuan Menulis', 'type' => 'N', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
				
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where);
		
	require_once(Route::getViewPath('inc_dataajax'));
?>
