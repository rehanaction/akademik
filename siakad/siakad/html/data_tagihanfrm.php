<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	/* $c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete']; */
	
	// include
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// koneksi database
	$connh = Query::connect('h2h');
	$connh->debug = $conn->debug;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Tagihan Formulir';
	$p_tbwidth = 600;
	$p_aktivitas = 'KULIAH';
	$p_listpage = Route::getListPage();
	
	$p_model = mTagihan;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// cek data
	if(!empty($r_key)) {
		$g_lunas = $p_model::getLunasFrm($connh,$r_key);
		
		if($g_lunas) {
			$c_edit = false;
			$c_delete = false;
		}
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'transactionid', 'label' => 'ID Transaksi', 'maxlength' => 60, 'size' => 30);
	$a_input[] = array('kolom' => 'frmid', 'label' => 'ID Formulir', 'type' => 'S', 'option' => $p_model::billCodeFrm($connh));
	$a_input[] = array('kolom' => 'idpend', 'label' => 'ID Pendaftar', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'namapend', 'label' => 'Nama Pendaftar', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'pin', 'label' => 'PIN', 'maxlength' => 32, 'size' => 30);
	$a_input[] = array('kolom' => 'accesstime', 'label' => 'Waktu Akses', 'type' => 'DT', 'readonly' => true);
	$a_input[] = array('kolom' => 'billamount', 'label' => 'Jumlah', 'type' => 'N', 'maxlength' => 19, 'size' => 20);
	$a_input[] = array('kolom' => 'lunas', 'label' => 'Lunas', 'type' => 'R', 'option' => array('' => 'Belum Lunas', '1' => 'Lunas'));
	
	// ada aksi
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['billcode'] = $p_model::billher;
		
		if(empty($r_key))
			$err = $p_model::insertCRecordFrm($connh,$a_input,$record);
		else
			$err = $p_model::updateCRecordFrm($connh,$a_input,$record,$r_key);
		
		if(!$err)
			$r_key = $record['transactionid'];
		
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Penyimpanan '.strtolower($p_title).' '.($err ? 'gagal' : 'berhasil');
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		$err = $p_model::deleteFrm($connh,$r_key);
		
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Penghapusan '.strtolower($p_title).' '.($err ? 'gagal' : 'berhasil');
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEditFrm($connh,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>