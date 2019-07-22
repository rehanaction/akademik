<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jawabankategorikuis'));
	require_once(Route::getModelPath('kategorikuis'));
	require_once(Route::getUIPath('combo'));

	// variabel request
	$r_idkategori = Modul::setRequest($_POST['idkategori'],'IDKATEGORI');
	$l_idkategori = uCombo::kategorikuis($conn,$r_idkategori,'idkategori','onchange="goSubmit()"',false);

	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idkategori', 'label' => 'Kategori', 'type' => 'S','option'=>mKategoriKuis::getArray($conn));
	$a_kolom[] = array('kolom' => 'kodejawaban', 'label' => 'Kode', 'size' => 10, 'maxlength' => 10, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'teksjawaban', 'label' => 'Text', 'size' => 50, 'maxlength' => 50, 'notnull' => true);
        
	// properti halaman
	$p_title = 'Daftar Jawaban Kategori kuis';
	$p_tbwidth = 850;
	
	$p_model = mJawabanKategoriKuis;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);

	// mendapatkan data
	if(!empty($r_idkategori)) 
		$a_filter[] = $p_model::getListFilter('idkategori',$r_idkategori);

	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);

	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Kategori', 'combo' => $l_idkategori);

	
	require_once($conf['view_dir'].'inc_ms.php');
?>
