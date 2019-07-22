<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// include
	require_once(Route::getModelPath('kategoribeasiswa'));
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Data Kategori Beasiswa';
	$p_tbwidth = 600;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = true; // empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// combo
	$a_jenistagihan = mJenistagihan::getDatacombo($conn);
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodekategori', 'label' => 'Kode Kategori', 'maxlength' => 10, 'size' => 20);
	$a_input[] = array('kolom' => 'namakategori', 'label' => 'Nama Kategori', 'maxlength' => 50, 'size' => 20);
	
	$p_model = mKategoriBeasiswa;
        
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST,'','',false);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input, $record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input, $record,$r_key);
		
		if(!$p_posterr and empty($r_key))
			$r_key = $record['kodekategori'];
	}
	else if($r_act == 'delete' and $c_delete) { 
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'addpotongan' and $c_edit) {
		$record = array();
		$record['kodekategori'] = $r_key;
		$record['jenistagihan'] = $_POST['pot_jenis'];
		$record['jumlahpotongan'] = $_POST['pot_jumlah'];
		$record['ispersen'] = (int)$_POST['pot_persen'];
		
		$p_posterr = $p_model::insertPotonganBeasiswa($conn,$record);
		
		$p_postmsg = 'Penambahan potongan beasiswa ';
		if($p_posterr)
			$p_postmsg .= 'gagal';
		else
			$p_postmsg .= 'berhasil';
	}
	else if($r_act == 'deletepotongan' and $c_edit) {
		$r_jenis = $_POST['keydet'];
		
		$p_posterr = $p_model::deletePotonganBeasiswa($conn,$r_key,$r_jenis);
		
		$p_postmsg = 'Penghapusan potongan beasiswa ';
		if($p_posterr)
			$p_postmsg .= 'gagal';
		else
			$p_postmsg .= 'berhasil';
	}
	
	if(!$p_posterr)
		unset($post);

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	// ambil data potongan beasiswa
	$a_potongan = $p_model::getListPotonganBeasiswa($conn,$r_key);
			
	require_once(Route::getViewPath('v_data_kategoribeasiswa'));
?>