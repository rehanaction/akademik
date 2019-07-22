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
	$p_title = 'Data Voucher';
	$p_tbwidth = 600;
	$p_aktivitas = 'KEUANGAN';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	
	// cek jenis deposit dan id tagihan
	if(!empty($r_key)) {
		$row = $p_model::getData($conn,$r_key);
		if(mAkademik::isRolePMB() and empty($row['nopendaftar']))
			unset($r_key,$row);
		else if($row['jenisdeposit'] != 'V')
			Route::navigate('data_deposit&key='.$r_key);
		else if(empty($row['idtagihan']))
			$istagihan = false;
		else
			$istagihan = true;
	}
	
	//struktur view
	$a_input = array();
	if(!empty($r_key))
		$a_input[] = array('kolom' => 'novoucher', 'label' => 'No. Voucher', 'maxlength' => 10, 'size' => 10, 'readonly' => true);
	$a_input[] = array('kolom' => 'nimpendaftar', 'label' => '(Calon) Mahasiswa', 'type' => 'X', 'text' => 'nama', 'param' => 'strpost:"f=acmhspendaftarunit"', 'readonly' => $istagihan);
	$a_input[] = array('kolom' => 'nominaldeposit', 'label' => 'Nominal Voucher', 'maxlength' => 12, 'size' => 12, 'type' => 'N');
	if(!empty($r_key))
		$a_input[] = array('kolom' => 'nominalpakai', 'label' => 'Jumlah Dipakai', 'maxlength' => 12, 'size' => 12, 'type' => 'N', 'readonly' => true);
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode', 'type'=> 'S', 'option'=> mCombo::periode($conn), 'readonly' => $istagihan);
	if(!empty($r_key))
		$a_input[] = array('kolom' => 'isfromtagihan', 'label' => 'Dari Tagihan?', 'type' => 'C', 'option' => array('-1' => 'Voucher berasal dari tagihan'), 'readonly' => true);
	if($istagihan)
		$a_input[] = array('kolom' => 'idtagihan', 'label' => 'Tagihan', 'maxlength' => 24, 'size' => 24, 'readonly' => true, 'link' => 'data_tagihan');
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'S', 'option' => array('-1'=>'Aktif', '0'=>'Tidak Aktif'));
	$a_input[] = array('kolom' => 'tglexpired', 'label' => 'Tgl Expired', 'type' => 'D');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A');
        
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		// cek nimpendaftar
		list($t_jenis,$t_nim) = explode(':',$_POST['nimpendaftar']);
		if($t_jenis == 'p')
			$t_kolom = 'nopendaftar';
		else
			$t_kolom = 'nim';
		
		$_POST[$t_kolom] = $t_nim;
		$a_input[] = array('kolom' => $t_kolom);
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)) {
			$record['jenisdeposit'] = 'V';
			$record['tgldeposit'] = date('Y-m-d');
		}
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input, $record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input, $record,$r_key);
		
		// hapus lagi ceknya
		array_pop($a_input);
		
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
