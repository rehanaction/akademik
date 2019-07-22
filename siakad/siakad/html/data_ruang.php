<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('ruang'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Ruang Kelas';
	$p_tbwidth = 500;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mRuang;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'koderuang', 'label' => 'Kode Ruang', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'lokasi', 'label' => 'Nama Ruang', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Unit', 'type' => 'S', 'option' => mCombo::unit($conn,false));
	$a_input[] = array('kolom' => 'lantai', 'label' => 'Lantai', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'luasruangan', 'label' => 'Luas', 'type' => 'N', 'maxlength' => 4, 'size' => 5);
	$a_input[] = array('kolom' => 'dayatampung', 'label' => 'Kapasitas', 'type' => 'N', 'maxlength' => 3, 'size' => 5);
	$a_input[] = array('kolom' => 'ipruang', 'label' => 'IP Komputer Ruangan', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif ?', 'type' => 'C', 'option' => array('-1' => ''));

	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['isaktif']=$record['isaktif']=='null'?0:$record['isaktif'];
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
