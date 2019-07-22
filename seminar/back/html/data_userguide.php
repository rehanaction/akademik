<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	 $c_insert = $a_auth['caninsert'];
	 $c_update = $a_auth['canupdate'];
	 //$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('userguide'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(!empty($r_key) and $c_update)
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Upload User Guide';
	$p_tbwidth = 600;
	$p_listpage = Route::getListPage();
	
	$p_model = mUserGuide;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);	

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;

	
	$a_input = array();
	$a_input[] = array('kolom' => 'namaguide', 'label' => 'Nama User Guide', 'size' => 30, 'maxlength' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'isfront', 'label' => 'Front', 'type' => 'C', 'option' => array(1=>0));
	// $a_input[] = array('kolom' => 'isvideo', 'label' => 'Video', 'type' => 'C', 'option' => array(1=>0));
	$a_input[] = array('kolom' => 'fileguide', 'label' => 'File User Guide', 'type' => 'U', 'uptype' => 'ug', 'size' => 40,'maxsize'=>'10','arrtype'=>array('png','doc','pdf','ppt','xls','xlsx','docx','rar','zip','mp4','mkv','flv','avi'));

	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();

		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		$namaposter = $_FILES['fileguide']['name'] ; 
		$tempposter = $_FILES['fileguide']['tmp_name'] ; 
		$record['fileguide'] = $namaposter;
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);

		if(!$p_posterr) unset($post);

		$ok = Query::isOK($p_posterr);

		$conn->CommitTrans($ok);
	}

	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
