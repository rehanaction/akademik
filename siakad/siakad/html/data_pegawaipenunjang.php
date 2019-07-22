<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pegawaipenunjang'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pegawai Penunjang';
	$p_tbwidth = 500;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mPegawaiPenunjang;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nopegawai', 'label' => 'No pegawai', 'maxlength' => 20, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'namapegawai', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'noidentitas', 'label' => 'Nomor Identitas', 'maxlength' => 20, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'nonpwp', 'label' => 'Nomor NPWP', 'type' => 'N', 'maxlength' => 25, 'size' => 10);
	$a_input[] = array('kolom' => 'notelephon', 'label' => 'Nomor Telephon', 'maxlength' => 15, 'size' => 10);
	$a_input[] = array('kolom' => 'nohp', 'label' => 'Nomor HP','maxlength' => 15, 'size' => 10);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 50,'size' => 10);
	$a_input[] = array('kolom' => 'norekening', 'label' => 'Nomor Rekening', 'type' => 'N,1','maxlength' => 50, 'size' => 20);
	$a_input[] = array('kolom' => 'namarekening', 'label' => 'Nama Pemilik Rekening', 'maxlength' => 100, 'size' => 20);
	$a_input[] = array('kolom' => 'cabangrekening', 'label' => 'Cabang Rekening', 'maxlength' => 100, 'size' => 20);
	$a_input[] = array('kolom' => 'biatrans', 'label' => 'Biaya Transfer', 'type' => 'N', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'pajak', 'label' => 'Pajak', 'type' => 'N,2', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'isasisten', 'label' => 'Asisten ?', 'type' => 'C', 'option' => array('-1'=>''));
	
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
