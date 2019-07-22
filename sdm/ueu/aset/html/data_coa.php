<?php	
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_coa');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('coa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$pkey = CStr::removeSpecial($_REQUEST['pkey']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data COA';
	$p_tbwidth = 500;
	$p_aktivitas = 'COA';
	$p_dbtable = 'ms_coa';
	$p_key = 'idcoa';
	$p_listpage = Route::getListPage();
	
	$p_model = mCoa;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	

	//cek bila add child, lalu ambil field dari parent
	/*if(!empty($pkey)){
		$pcat = $p_model::pCoa($conn,$pkey);
	} else {
		$pcat['level'] = '1';
	}
	
	if(empty($pkey)){
		$islevelreadonly = true;
	} else {
		$islevelreadonly = false;
	}*/
	
	//cek bila add child, lalu ambil field dari parent
    $a_parent = array();
	if(empty($pkey)){
		$a_parent = array('idcoa' => null, 'level' => 0);
	}else{
		$a_parent = $p_model::getData($conn,$pkey);
	}
	
	$isreadonly = !($r_act == 'save' and empty($r_key));
	
	$a_level = $p_model::level();
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idcoa', 'label' => 'ID.', 'size' => 15, 'maxlength' => 20, 'notnull' => true);
	$a_input[] = array('kolom' => 'namacoa', 'label' => 'Nama COA', 'size' => 30,  'maxlength' => 45, 'notnull' => true);
	//$a_input[] = array('kolom' => 'idparent', 'label' => 'Parent', 'readonly' => true, 'default' => $pkey, 'hiddenval' => $pkey, 'issave' => true);
	//$a_input[] = array('kolom' => 'level', 'label' => 'Level', 'readonly' => true, 'default' => $a_level[$pcat['level']] , 'hiddenval' => $pcat['level'], 'issave' => true);
	$a_input[] = array('kolom' => 'idparent', 'label' => 'Parent', 'readonly' => true, 'default' => $a_parent['idcoa'], 'issave' => true);
	$a_input[] = array('kolom' => 'level', 'label' => 'Level', 'readonly' => true, 'default' => (int)$a_parent['level']+1, 'issave' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){ 
			//$record['idparent'] = $_POST['idparent'];
			//$record['level'] = $_POST['level'];
			$record['level'] = (int)$a_parent['level']+1;
		    $record['idparent'] = $a_parent['idcoa'];
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){			
			unset($post);
			$pkey = '';
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
