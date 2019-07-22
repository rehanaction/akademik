<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('matakuliah'));
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
	$p_title = 'Data Mata Kuliah';
	$p_tbwidth = 600;
	$p_aktivitas = 'KULIAH';
	$p_listpage = Route::getListPage();
	
	$p_model = mMatakuliah;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	//die('ok');
	// cek data
	$a_kurikulum = mCombo::kurikulum($conn);
	
	$r_act = $_POST['act'];
	if(empty($r_key) or $r_act == 'change') {
		$post['thnkurikulum'] = Modul::setRequest($_POST['thnkurikulum'],'KURIKULUM');
		
		$r_kurikulum = $post['thnkurikulum'];
		if(!isset($a_kurikulum[$r_kurikulum]))
			$r_kurikulum = key($a_kurikulum);
	}
	else {
		$a_cek = $p_model::getData($conn,$r_key);
		
		$r_kurikulum = $a_cek['thnkurikulum'];
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum', 'type' => 'S', 'option' => $a_kurikulum, 'add' => 'onchange="goChange()"');
	$a_input[] = array('kolom' => 'kodemk', 'label' => 'Kode Mata Kuliah', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'namamk', 'label' => 'Nama Mata Kuliah', 'maxlength' => 65, 'size' => 65);
	$a_input[] = array('kolom' => 'sks', 'label' => 'SKS', 'type' => 'N', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'skstatapmuka', 'label' => 'SKS Tatap Muka', 'type' => 'N', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'skspraktikum', 'label' => 'SKS Praktikum', 'type' => 'N', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'nilaimin', 'label' => 'Nilai Minimal', 'type' => 'S', 'option' => mCombo::nAngkaKurikulum($conn,$r_kurikulum));
	$a_input[] = array('kolom' => 'skslulusmin', 'label' => 'SKS Lulus Minimal', 'type' => 'N', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'tipekuliah', 'label' => 'Jenis Kuliah', 'type' => 'S', 'option' => $p_model::tipeKuliah());
	$a_input[] = array('kolom' => 'kodejenis', 'label' => 'Kompetensi', 'type' => 'S', 'option' => $p_model::jenisMataKuliah($conn));
	$a_input[] = array('kolom' => 'nipdosenpengampu', 'label' => 'Dosen Pengampu', 'type' => 'X', 'text' => 'dosenpengampu', 'param' => 'strpost:"f=acpegawai"');
	$a_input[] = array('kolom' => 'abstrakmk', 'label' => 'Abstrak', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'isagama', 'label' => 'Mata kuliah Agama?', 'type' => 'S', 'option' => mCombo::agama());
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Status Mata Kuliah', 'type' => 'S', 'option' => mCombo::aktif());
	$a_input[] = array('kolom' => 'istoefl', 'label' => 'Termasuk Toefl ?', 'type' => 'S', 'option' => mCombo::istoefl());
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
