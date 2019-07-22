<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = false;
	
	// include
	require_once(Route::getModelPath('strukturkegiatan'));
	require_once(Route::getModelPath('ketkegiatan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Struktur Kegiatan';
	$p_tbwidth = 550;
	$p_aktivitas = 'struktur kegiatan';
	$p_listpage = Route::getListPage();
	
	$p_model = mStrukturKegiatan;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	/* if(!empty($_REQUEST['parent']))
		$a_parentkodekegiatan = $p_model::getArray($conn,$_REQUEST['parent']);
	else
		$a_parentkodekegiatan = array(null=>'')+$p_model::getArray($conn); */
	
	$a_parentkodekegiatan = $p_model::getListCombo($conn,$_REQUEST['parent']);
	if(empty($_REQUEST['parent']))
		$a_parentkodekegiatan = array('' => '') + $a_parentkodekegiatan;
	
	$a_input = array();
	$a_input[] = array('kolom' => 'nokegiatan', 'label' => 'Kode Kegiatan', 'maxlength' => 20, 'size' => 20);
	//$a_input[] = array('kolom' => 'kodekegiatan', 'label' => 'Kode Kegiatan', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan', 'maxlength' => 100, 'size' => 50);
	//$a_input[] = array('kolom' => 'Merupakan Induk', 'label' => 'isparent?', 'type' => 'C', 'option' => array('-1' => ''));
	$a_input[] = array('kolom' => 'parentkodekegiatan', 'label' => 'Induk', 'type' => 'S', 'option' => $a_parentkodekegiatan, 'add' => 'style="width:350px"');
	//$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan');

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		if(!empty($_POST['isparent']))
			unset($record['parentkodekegiatan']);
			
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) {
			unset($post);
			
			// set readonly parent (manual index)
			$a_input[2]['readonly'] = true;
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
