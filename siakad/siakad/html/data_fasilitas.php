<?php 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('fasilitas'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Fasilitas';
	$p_tbwidth = 600;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mFasilitas;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Unit', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'notnull' => true);
	$a_input[] = array('kolom' => 'luastanah', 'label' => 'Luas Tanah','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luaskebun', 'label' => 'Luas Kebun','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luasrkuliah', 'label' => 'Luas Ruang Kuliah','type' => 'N', 'maxlength' => 9, 'size' => 9);	
	$a_input[] = array('kolom' => 'luasrlab', 'label' => 'Luas Ruang Lab','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'jumlahrlab', 'label' => 'Jumlah Ruang Lab','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luasrdosen', 'label' => 'Jumlah Ruang Dosen','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luasradministrasi', 'label' => 'Luas Ruang Administrasi','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luasrseminar', 'label' => 'Luas Ruang Seminar','type' => 'N', 'maxlength' => 9, 'size' => 9); 
	$a_input[] = array('kolom' => 'luasperumahan', 'label' => 'Luas Perumahan','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luasrasramamhs', 'label' => 'Luas Ruang Arama Mahasiswa','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luasraula', 'label' => 'Luas Ruang Aula','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luasrkomputer', 'label' => 'Luas Ruang Komputer','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'jumlahrkomputer', 'label' => 'Jumlah Ruang Komputer','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luasrekskulmhs', 'label' => 'Luas Ruang Ekskul Mahasiswa','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'jumlahrekskulmhs', 'label' => 'Jumlah Luas Ruang Ekskul Mahasiswa','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'luasrperpus', 'label' => 'Luas Ruang Perpustakaan','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'jumlahrperpus', 'label' => 'Jumlah Luas Ruang Perpustakaan','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'jumlahjuduldigunakan', 'label' => 'Jumlah Judul Digunakan','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'judulpustaka', 'label' => 'Judul Pustaka','type' => 'N', 'maxlength' => 9, 'size' => 9);
	$a_input[] = array('kolom' => 'jumlahjudulpustaka', 'label' => 'Jumlah Judul Pustaka','type' => 'N', 'maxlength' => 9, 'size' => 9);	
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