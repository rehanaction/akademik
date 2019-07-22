<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	 //$c_insert = $a_auth['caninsert'];
	 $c_update = $a_auth['canupdate'];
	 //$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(!empty($r_key) and $c_update)
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Tanda Tangan';
	$p_tbwidth = 640;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mUnit;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$uptype = 'tandatangan';
	
	$a_ketua = mCombo::dosen($conn,$r_key);
	

	
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Kode Unit', 'maxlength' => 5, 'size' => 5, 'readonly' => true);
	$a_input[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit', 'maxlength' => 100, 'size' => 50, 'readonly' => true);
	// $a_input[] = array('kolom' => 'nama', 'label' => 'Ketua', 'readonly' => true);
	$a_input[] = array('kolom' => 'tandatangan', 'label' => 'Gambar', 'type' => 'U', 'uptype' => $uptype, 'size' => 40,'maxsize'=>'10','arrtype'=>array('png','jpg','gif'));

	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();

		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		$namaposter = $_FILES['tandatangan']['name'] ; 
		$tempposter = $_FILES['tandatangan']['tmp_name'] ; 
		$record['tandatangan'] = $namaposter;
		
		
		list($p_posterr) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);

		if(!$p_posterr) unset($post);

		$ok = Query::isOK($p_posterr);

		$conn->CommitTrans($ok);
	}

	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
