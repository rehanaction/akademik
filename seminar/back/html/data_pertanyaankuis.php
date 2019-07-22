<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pertanyaankuisseminar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pertanyaan Kuisioner';
	$p_tbwidth = 800;
	$p_aktivitas = 'KULIAH';
	$p_listpage = Route::getListPage();
	
	$p_model = mPertanyaanKuisSeminar;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
	$r_act = $_POST['act'];
	
	$aktif=array('1'=>'Aktif','0'=>'Tidak Aktif');
	
	// struktur view	
	$a_input = array();
	$a_input[] = array('kolom' => 'idseminar', 'label' => 'Seminar', 'type' => 'S', 'option' => mCombo::seminar($conn), 'request' => 'SEMESTER');
	$a_input[] = array('kolom' => 'nomor', 'label'=>'Nomor Pertanyaan', 'size'=>3,'maxlength'=>3);
	$a_input[] = array('kolom' => 'pertanyaan','label'=>'Pertanyaan',  'size'=>70,'maxlength'=>250,'empty'=>false);
	$a_input[] = array('kolom' => 'idkategori', 'label' => 'Kategori', 'type' => 'S', 'option' => mCombo::kategorikuis($conn));
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Status Soal', 'type' => 'S', 'option' => $aktif);

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
