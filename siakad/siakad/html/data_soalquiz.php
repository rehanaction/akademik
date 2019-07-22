<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('soalquiz'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Soal Quisioner';
	$p_tbwidth = 600;
	$p_aktivitas = 'KULIAH';
	$p_listpage = Route::getListPage();
	
	$p_model = mSoalQuiz;
	
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
	$aktif=array('1'=>'Aktif','0'=>'Tidak Aktif');
	
	// struktur view
	
	$a_input = array();
	$a_input[] = array('kolom' => 'semester', 'label' => 'Semester', 'type' => 'S', 'option' => mCombo::semester(), 'request' => 'SEMESTER');
	$a_input[] = array('kolom' => 'tahun','label'=>'Tahun', 'type' => 'S', 'option' => mCombo::tahun(), 'request' => 'TAHUN');
	$a_input[] = array('kolom' => 'idjenissoal','label'=>'Jenis Soal', 'type' => 'S', 'option' => mCombo::jenisQuiz($conn),'empty'=>false);
	$a_input[] = array('kolom' => 'soal', 'label' => 'Soal', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status Soal', 'type' => 'S', 'option' => $aktif);
	$a_input[] = array('kolom' => 'bobot', 'label' => 'Bobot Pertanyaan', 'type' => 'NP', 'maxlength' => 3, 'notnull' => true);



	// ada aksi
	if($r_act == 'save' and $c_edit) {
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['periode'] = $record['tahun'].$record['semester']; 
		
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
