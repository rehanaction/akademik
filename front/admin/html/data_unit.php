<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//ini_set("display_errors",true);
	$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Satuan Kerja';
	$p_tbwidth = 550;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mUnit;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_dosen = mCombo::dosenKetua($conn,'');
	
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Kode Unit', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'namauniten', 'label' => 'Nama Unit (en)', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'kodeunitparent', 'label' => 'Induk', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'readonly' => empty($r_key),'empty'=>true);
	$a_input[] = array('kolom' => 'isakad', 'label' => 'Termasuk Unit Akademik?', 'type' => 'R', 'option' => mCombo::unitAkad());
	$a_input[] = array('kolom' => 'ispamu', 'label' => 'Pengelola Kelas Bersama ?', 'type' => 'R', 'option' => mCombo::unitAkad());
	$a_input[] = array('kolom' => 'namaketuasementara', 'label' => 'Nama dan Gelar Ketua ', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'nipketuasementara', 'label' => 'Nip Ketua ', 'maxlength' => 100, 'size' => 30);
	//$a_input[] = array('kolom' => 'ketua', 'label' => 'Ketua', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
	//$a_input[] = array('kolom' => 'sekretaris', 'label' => 'Sekretaris', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
	//$a_input[] = array('kolom' => 'pembantu1', 'label' => 'Pembantu Bidang 1', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
	//$a_input[] = array('kolom' => 'pembantu2', 'label' => 'Pembantu Bidang 2', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
	//$a_input[] = array('kolom' => 'pembantu3', 'label' => 'Pembantu Bidang 3', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
	
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
	
	require_once(Route::getViewPath('inc_data'));
?>
