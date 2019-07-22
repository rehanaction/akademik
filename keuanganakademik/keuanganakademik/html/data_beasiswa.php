<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$p_model = mBeasiswa;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Beasiswa';
	$p_tbwidth = 600;
	$p_aktivitas = 'KEUANGAN';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nim', 'label' => 'NIM', 'readonly' => (empty($r_key) ? false : true));
	if(!empty($r_key)) {
		$a_input[] = array('kolom' => 'nama', 'label' => 'Nama Mahasiswa', 'readonly' => true);
		$a_input[] = array('kolom' => 'namaunit', 'label' => 'Unit', 'readonly' => true);
		$a_input[] = array('kolom' => 'angkatan', 'label' => 'Angkatan', 'readonly' => true);
	}
	$a_input[] = array('kolom' => 'potongan', 'label' => 'Nominal Beasiswa / smt', 'size' => 12, 'maxlength' => 12, 'type' => 'N');
	$a_input[] = array('kolom' => 'potsmtawal', 'label' => 'Semester Awal', 'size' => 2, 'maxlength' => 2, 'type' => 'N', 'default' => 1);
	$a_input[] = array('kolom' => 'potsmtakhir', 'label' => 'Semester Akhir', 'size' => 2, 'maxlength' => 2, 'type' => 'N', 'default' => 8);
	$a_input[] = array('kolom' => 'keteranganbeasiswa', 'label' => 'Keterangan', 'type' => 'A');
        
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			$t_key = $record['nim'];
		else
			$t_key = $r_key;
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$t_key);
		
		if(!$p_posterr and empty($r_key))
			$r_key = $t_key;
	}
	else if($r_act == 'delete' and $c_delete) { 
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	if(!$p_posterr)
		unset($post);

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
			
	require_once(Route::getViewPath('v_data_deposit'));
?>