<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kurikulum'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Kurikulum';
	$p_tbwidth = 600;
	$p_aktivitas = 'KULIAH';
	$p_listpage = Route::getListPage();
	
	$p_model = mKurikulum;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// cek data
	$a_kodeunit = mCombo::unit($conn,false);
	$a_kurikulum = mCombo::kurikulum($conn);
	
	$r_act = $_POST['act'];
	if(empty($r_key) or $r_act == 'change') {
		$post['kodeunit'] = Modul::setRequest($_POST['kodeunit'],'UNIT');
		$post['thnkurikulum'] = Modul::setRequest($_POST['thnkurikulum'],'KURIKULUM');
		
		$r_kodeunit = $post['kodeunit'];
		if(!isset($a_kodeunit[$r_kodeunit]))
			$r_kodeunit = key($a_kodeunit);
		
		$r_kurikulum = $post['thnkurikulum'];
		if(!isset($a_kurikulum[$r_kurikulum]))
			$r_kurikulum = key($a_kurikulum);
	}
	else {
		$a_cek = $p_model::getData($conn,$r_key);
		
		$r_kodeunit = $a_cek['kodeunit'];
		$r_kurikulum = $a_cek['thnkurikulum'];
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Prodi', 'type' => 'S', 'option' => $a_kodeunit, 'add' => 'onchange="goChange()"');
	$a_input[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum', 'type' => 'S', 'option' => $a_kurikulum, 'add' => 'onchange="goChange()"');
	//$a_input[] = array('kolom' => 'kodemk','label' => 'Mata Kuliah', 'type' => 'S', 'option' => $p_model::mkKurikulum($conn,$r_kurikulum,$r_kodeunit), 'add' => 'style="width:400px"');
	$a_input[] = array('kolom' => 'kodemk', 'label' => 'Mata Kuliah', 'type' => 'X', 'text' => 'matkul','param'=>'strpost:"f=acmatkulkurikulum&kurikulum='.$r_kurikulum.'"');
	$a_input[] = array('kolom' => 'sks', 'label' => 'SKS', 'type' => 'N', 'maxlength' => 3, 'size' => 3, 'readonly' => true);
	$a_input[] = array('kolom' => 'semmk', 'label' => 'Semester', 'type' => 'N', 'maxlength' => 2, 'size' => 2);
	$a_input[] = array('kolom' => 'semmk_old', 'label' => 'Semester KRS', 'type' => 'N', 'maxlength' => 2, 'size' => 2);
	$a_input[] = array('kolom' => 'wajibpilihan', 'label' => 'Wajib/Pilihan', 'type' => 'C', 'option' => array('W' => ''));
	$a_input[] = array('kolom' => 'paket', 'label' => 'Konsentrasi', 'type' => 'C', 'option' => array('1' => ''));
	
	// ada aksi
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		if($record['wajibpilihan']=='null')
			$record['wajibpilihan']='P';
		if($record['paket']=='null')
			$record['paket']=0;
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
	
	// ambil data submit
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
