<?php 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;


	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jenispegawai'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Jenis Pegawai';
	$p_tbwidth = 700;
	$p_aktivitas = 'Master Data';
	$p_listpage = Route::getListPage();
	
	$p_model = mJenispegawai;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idjenispegawai', 'label' => 'Kode Jenis Pegawai');
	$a_input[] = array('kolom' => 'idtipepeg', 'label' => 'Kode Tipe Pegawai', 'type' => 'S', 'option' => mCombo::tipePegawai($conn));
	$a_input[] = array('kolom' => 'jenispegawai', 'label' => 'Nama Jenis','notnull' => true);
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif ?','notnull' => true, 'type' => 'S', 'option' => mCombo::aktif());
	$a_input[] = array('kolom' => 'isnaikpangkat', 'label' => 'Naik Pangkat ?');
	$a_input[] = array('kolom' => 'koderole', 'label' => 'Kode Role', 'type' => 'S', 'option' => mCombo::koderoles($conn), 'empty' => true);
	// ada aksi
	$r_act = $_POST['act'];
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
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>