<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	 //$c_insert = $a_auth['caninsert'];
	 $c_update = $a_auth['canupdate'];
	 //$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(!empty($r_key) and $c_update)
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Unit';
	$p_tbwidth = 640;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mUnit;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_dosen = mCombo::dosen($conn,'');
	
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Kode Unit', 'maxlength' => 5, 'size' => 5, 'readonly' => true);
	$a_input[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit', 'maxlength' => 100, 'size' => 50, 'readonly' => true);
	$a_input[] = array('kolom' => 'kodeunitparent', 'label' => 'Induk', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'empty' => true, 'readonly' => true);
	$a_input[] = array('kolom' => 'ketua', 'label' => 'Ketua', 'type' => 'S', 'option' => $a_dosen, 'empty' => true, 'readonly' => true);
	$a_input[] = array('kolom' => 'sekretaris', 'label' => 'Sekretaris', 'type' => 'S', 'option' => $a_dosen, 'empty' => true, 'readonly' => true);
	$a_input[] = array('kolom' => 'pembantu1', 'label' => 'Pembantu Bidang 1', 'type' => 'S', 'option' => $a_dosen, 'empty' => true, 'readonly' => true);
	$a_input[] = array('kolom' => 'pembantu2', 'label' => 'Pembantu Bidang 2', 'type' => 'S', 'option' => $a_dosen, 'empty' => true, 'readonly' => true);
	$a_input[] = array('kolom' => 'pembantu3', 'label' => 'Pembantu Bidang 3', 'type' => 'S', 'option' => $a_dosen, 'empty' => true, 'readonly' => true);
	$a_input[] = array('kolom' => 'kodenim', 'label' => 'Kode NIM (Untuk NIM Mhs)', 'maxlength' => 1, 'size' => 1);

	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$up= $p_model::updateKodenim($conn,$a_input,$record,$r_key);
		if($up)
			list($p_posterr,$p_postmsg)=$up;
		else{
			$p_posterr=true;
			$p_postmsg="Edit Hanya Berlaku Untuk Unit Siakad";
		}
		if(!$p_posterr) unset($post);
	}

	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
