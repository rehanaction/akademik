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
	require_once(Route::getModelPath('deposit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$p_model = mDeposit;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Deposit Mahasiswa';
	$p_tbwidth = 600;
	$p_aktivitas = 'KEUANGAN';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	
	// cek jenis deposit
	if(!empty($r_key) and $p_model::getJenisDeposit($conn,$r_key) == 'V')
		Route::navigate('data_voucher&key='.$r_key);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nim', 'label' => 'NIM', 'maxlength' => 20, 'size' => 20, 'notnull'=>true);
	$a_input[] = array('kolom' => 'nominaldeposit', 'label' => 'Nominal Deposit', 'maxlength' => 12, 'size' => 12, 'type' => 'N', 'notnull'=>true);
	if(!empty($r_key))
		$a_input[] = array('kolom' => 'nominalpakai', 'label' => 'Jumlah Dipakai', 'maxlength' => 12, 'size' => 12, 'type' => 'N', 'readonly' => true);
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode', 'type'=> 'S', 'option'=> mCombo::periode($conn));
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'S', 'option' => array('-1'=>'Aktif', '0'=>'Tidak Aktif'));
	//$a_input[] = array('kolom' => 'tglexpired', 'label' => 'Tgl Expired', 'type' => 'D');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A' ,'notnull'=>true);
        
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			$record['tgldeposit'] = date('Y-m-d');
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input, $record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input, $record,$r_key);
		
		if(!$p_posterr and empty($r_key))
			$r_key = $p_model::getLastValue($conn);
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
