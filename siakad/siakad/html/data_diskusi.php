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
	require_once(Route::getModelPath('diskusi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$p_model = mDiskusi;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_kelas = CStr::removeSpecial($_REQUEST['kelas']);
	$r_topik = CStr::removeSpecial($_REQUEST['topik']);
	
	if(!empty($r_key)) {
		$a_cek = $p_model::getData($conn,$r_key);
		
		$r_kelas = mKelas::getKeyRow($a_cek);
		$r_topik = $a_cek['idtopik'];
	}
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Forum Diskusi';
	$p_tbwidth = 600;
	$p_aktivitas = 'FORUM';
	
	if(empty($r_key))
		$p_listpage = 'list_diskusikelas';
	else
		$p_listpage = 'list_subdiskusikelas';
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_kelas))
		Route::navigate('list_diskusi');
	else if($c_readlist) {
		if(empty($r_key))
			$p_listpage .= ('&kelas='.$r_kelas.'&topik='.$r_topik);
		else
			$p_listpage .= ('&key='.$r_key);
	}
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// struktur view
	$a_infokelas = mKelas::getDataSingkat($conn,$r_kelas);
	$p_kelas = $a_infokelas['kodemk'].' - '.$a_infokelas['namamk'].' ('.$a_infokelas['kelasmk'].')';
	
	$a_input = array();
	$a_input[] = array('kolom' => 'kelas', 'label' => 'Mata Kuliah', 'default' => $p_kelas, 'readonly' => true);
	
	if(!empty($r_key))
		$a_input[] = array('kolom' => 'waktuposting', 'label' => 'Waktu Posting', 'type' => 'DT', 'readonly' => true);
	
	$a_input[] = array('kolom' => 'idtopik', 'label' => 'Topik', 'type' => 'S', 'option' => $p_model::topik($conn,true), 'notnull' => true, 'default' => $r_topik, 'infoedit' => 'Pilih topik anak');
	$a_input[] = array('kolom' => 'judulforum', 'label' => 'Judul Diskusi', 'maxlength' => 255, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'isi', 'label' => 'Isi Diskusi', 'type' => 'M');
	$a_input[] = array('kolom' => 'lockforum', 'label' => 'Kunci Diskusi', 'type' => 'C', 'option' => array('t' => ''));
	
	// hidden value
	$a_hidden = array();
	$a_hidden['kelas'] = $r_kelas;
	$a_hidden['topik'] = $r_topik;
	
	// ada aksi
	$r_act = $_POST['act'];	
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$conn->BeginTrans();
		
		foreach ($a_input as $key => $value) {
			if($value['type']=='M')
				$record[$value['kolom'].':skip'] = true;
		}

		if(empty($r_key)) {
			$record['creator'] = Modul::getUserName();
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
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
