<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$c_validasi = $a_auth['canother']['V'];
	$c_pengumuman = $a_auth['canother']['P'];
	
	// include
	require_once(Route::getModelPath('berita'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Publikasi';
	$p_tbwidth = 600;
	$p_aktivitas = 'BERITA';
	
	$p_model = mBerita;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// cek data
	if(!empty($r_key)) {
		$a_cek = $p_model::getData($conn,$r_key);
		
		if(($a_cek['creator'] != Modul::getUserName() or !empty($a_cek['validator'])) and !$c_validasi) {
			$c_edit = false;
			$c_delete = false;
		}
	}
	
	// struktur view
	$a_input = array();
	
	if(!empty($r_key)) {
		$a_input[] = array('kolom' => 'waktuposting', 'label' => 'Waktu Posting', 'type' => 'DT', 'readonly' => true);
		$a_input[] = array('kolom' => 'penulis', 'label' => 'Penulis', 'default' => $a_cek['namacreator'], 'readonly' => true);
	}
	
	$a_input[] = array('kolom' => 'jenis', 'label' => 'Jenis', 'type' => 'S', 'option' => $p_model::jenisBerita($c_pengumuman));
	$a_input[] = array('kolom' => 'judulberita', 'label' => 'Judul', 'maxlength' => 255, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'sumber', 'label' => 'Sumber', 'maxlength' => 255, 'size' => 50);
	$a_input[] = array('kolom' => 'isi', 'label' => 'Isi Berita', 'type' => 'M');
	$a_input[] = array('kolom' => 'gambar', 'label' => 'Gambar', 'type' => 'UI', 'uptype' => 'berita', 'size' => 40);
	$a_input[] = array('kolom' => 'tglexpired', 'label' => 'Kadaluarsa', 'type' => 'D');
	$a_input[] = array('kolom' => 'valid', 'label' => 'Validasi', 'type' => 'C', 'option' => array('-1' => ''), 'readonly' => !$c_validasi);
	
	// ada aksi
	$r_act = $_POST['act'];	
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		// isi tidak perlu di-filter xss
		foreach ($a_input as $key => $value) {
			if($value['type']=='M')
				$record[$value['kolom'].':skip'] = true;
		}
		
		if(empty($post['valid']) and !empty($a_cek['validator'])) {
			$record['validator'] = 'null';
			$record['waktuvalid'] = 'null';
		}
		else if(!empty($post['valid']) and empty($a_cek['validator'])) {
			$record['validator'] = Modul::getUserName();
			$record['waktuvalid'] = date('Y-m-d H:i:s');
		}
		
		if(empty($r_key)) {
			$record['creator'] = Modul::getUserName();
			$record['waktuposting'] = date('Y-m-d H:i:s');
			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		}
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'gambar');
	
	// cek data
	if(!empty($r_key)) {
		$a_cek = $p_model::getData($conn,$r_key);
		
		// tambahan info untuk struktur view
		if(!empty($a_cek['validator']))
			$a_input[8]['infoview'] = 'Oleh <strong>'.$a_cek['namavalidator'].'</strong> pada <strong>'.CStr::formatDateTimeInd($a_cek['waktuvalid'],true,true).'</strong>';
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>