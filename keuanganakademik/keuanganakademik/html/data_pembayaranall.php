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
	require_once(Route::getModelPath('pembayaran'));
	require_once(Route::getModelPath('pembayarandetail'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Pembayaran';
	$p_tbwidth = 600;
	$p_aktivitas = 'KEUANGAN';
	$p_listpage = Route::getListPage();
	$p_model = mPembayaran;
	$p_model2 = mPembayaranDetail;
	
	// cek pmb
	if(!empty($r_key)) {
		$data = $p_model::getData($conn,$r_key);
		
		if(mAkademik::isRolePMB() and empty($data['nopendaftar']))
			unset($r_key,$data);
	}
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idpembayaran', 'label' => 'ID Pembayaran');
	$a_input[] = array('kolom' => 'tglbayar', 'label' => 'Tgl Pembayaran','type'=>'D');
	$a_input[] = array('kolom' => 'jumlahbayar', 'label' => 'Jumlah','type'=> 'N');
	$a_input[] = array('kolom' => 'nip', 'label' => 'Petugas');
	$a_input[] = array('kolom' => 'ish2h', 'label' => 'Host to Host?', 'type' => 'C', 'option' => array('1' => ''));
	$a_input[] = array('kolom' => 'refno', 'label' => 'No. Ref.');
	$a_input[] = array('kolom' => 'idcurrency', 'label' => 'ID Currency');
	$a_input[] = array('kolom' => 'flagrekon', 'label' => 'flagrekon', 'type' => 'C', 'option' => array('1' => ''));
	$a_input[] = array('kolom' => 'periodebayar', 'label' => 'Periode Bayar');
	//$a_input[] = array('kolom' => 'companycode', 'label' => 'Company Code');
	//$a_input[] = array('kolom' => 'terminalid', 'label' => 'ID Terminal');
	//$a_input[] = array('kolom' => 'trekontime', 'label' => 'Waktu Rekon');
	//$a_input[] = array('kolom' => 'trxdatetime', 'label' => 'Waktu Rx');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan');
	$a_input[] = array('kolom' => 'flagbatal', 'label' => 'Batal?', 'type' => 'C', 'option' => array('1' => ''));
	
	//tabel detail pembayaran
	$t_detail = array();
	$t_detail[] = array('kolom' => 'idtagihan', 'label' => 'ID Tagihan',);
	$t_detail[] = array('kolom' => 'jenistagihan', 'label' => 'Jenis','align'=>'center');
	$t_detail[] = array('kolom' => 'nominalbayar', 'label' => 'Jumlah','align'=>'right','type'=>'N');
	$t_detail[] = array('kolom' => 'periode', 'label' => 'Periode','align'=>'center');
	//$t_detail[] = array('kolom' => 'denda', 'label' => 'Denda','align'=>'right');
	//$t_detail[] = array('kolom' => 'cicilanke', 'label' => 'Cicilan Ke','align'=>'center');
	
	$a_detail['pembayaran'] = array('key' => $p_model2::getDetailInfo('pembayaran','key'), 'data' => $t_detail);
	
	$rowd = array();
	$rowd += $p_model2::getPembayaranDetail($conn,$r_key,'pembayaran',$post);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
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
	
	require_once(Route::getViewPath('v_data_pembayaranall'));
?>