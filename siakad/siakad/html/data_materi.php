<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('materi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$p_model = mMateri;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_kelas = CStr::removeSpecial($_REQUEST['kelas']);
	
	if(!empty($r_key))
		$r_kelas = $p_model::getKeyKelas($conn,$r_key);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Forum Materi';
	$p_tbwidth = 600;
	$p_aktivitas = 'KULIAH';
	$p_listpage = 'list_materikelas';
	$p_uptype = $p_model::uptype;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_kelas))
		Route::navigate('list_materi');
	else if($c_readlist)
		$p_listpage .= ('&kelas='.$r_kelas);
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// struktur view
	$a_infokelas = mKelas::getDataSingkat($conn,$r_kelas);
	$p_kelas = $a_infokelas['kodemk'].' - '.$a_infokelas['namamk'].' ('.$a_infokelas['kelasmk'].')';
	
	$a_input = array();
	$a_input[] = array('kolom' => 'kelas', 'label' => 'Mata Kuliah', 'default' => $p_kelas, 'readonly' => true);
	
	if(!empty($r_key))
		$a_input[] = array('kolom' => 'waktuposting', 'label' => 'Waktu Posting', 'type' => 'DT', 'readonly' => true);
	
	$a_input[] = array('kolom' => 'judulmateri', 'label' => 'Judul', 'maxlength' => 255, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'isi', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 4, 'cols' => 40);
	$a_input[] = array('kolom' => 'filemateri', 'label' => 'File Materi', 'type' => 'U', 'uptype' => $p_uptype, 'size' => 40,'maxsize'=>'10','arrtype'=>array('doc','pdf','ppt','xls','rar','zip'));
	
	// ada aksi
	$r_act = $_POST['act'];	
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$conn->BeginTrans();
		
		if(empty($r_key)) {
			$record['waktuposting'] = date('Y-m-d H:i:s');
			$record += mKelas::getKeyRecord($r_kelas);
			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		}
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'file'.$p_uptype);
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
