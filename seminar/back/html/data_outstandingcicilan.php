<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = false; //$a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = false; //$a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pendaftar';
	$p_tbwidth = 600;
	$p_aktivitas = 'KULIAH';
	$p_listpage = Route::getListPage();
	
	$p_model = mTagihan;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
	
	$r_act = $_POST['act'];	
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nopendaftar', 'label' => 'No Pendaftar','readonly'=>true);
	$a_input[] = array('kolom' => 'nama', 'label' => 'Nama', 'readonly'=>true);
	$a_input[] = array('kolom' => 'hp', 'label' => 'HP', 'readonly'=>true);
	$a_input[] = array('kolom' => 'email', 'label' => 'E-Mail', 'readonly'=>true);
	$a_input[] = array('kolom' => 'pilihanditerima', 'label' => 'Jurusan', 'type' => 'S','option'=>mCombo::unitakademik($conn),'readonly'=>true);
	$a_input[] = array('kolom' => 'isfollowup', 'label' => 'Follow Up', 'type' => 'S', 'option'=>array(-1=>'Sudah', 0=>'Belum'), 'empty'=>true);
	$a_input[] = array('kolom' => 'keteranganpendaftar', 'label' => 'Keterangan', 'maxlength' => 4000,'rows'=>10, 'cols'=>70, 'type'=>'M');
	// ada aksi
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
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
