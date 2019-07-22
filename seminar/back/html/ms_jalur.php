<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	
	// include
	require_once(Route::getModelPath('jalur'));
	require_once(Route::getUIPath('combo'));
	// $conn->debug = true;
	// struktur view
	$a_kolom = array();
	//$a_kolom[] = array('kolom' => 'kodejalur', 'label' => 'Kode', 'size' => 2, 'maxlength' => 2, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'kodejalur', 'label' => 'Kode', 'size' => 3, 'maxlength' => 3, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Nama Jalur', 'size' => 10, 'maxlength' => 100, 'notnull' => true);
    $a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'size' => 20, 'maxlength' => 50, 'notnull' => false);
	//$a_kolom[] = array('kolom' => 'kodenim', 'label' => 'Kode NIM', 'size' => 2, 'maxlength' => 1, 'notnull' => true);
	
	//$a_kolom[] = array('kolom' => 'israport', 'label' => 'Tes Nilai Raport?', 'type' => 'R', 'option' => mCombo::pernahPonpes());
	$a_kolom[] = array('kolom' => 'israport', 'label' => 'Tes Nilai Raport?', 'type' => 'R', 'option' => mCombo::tesRaport());
	$a_kolom[] = array('kolom' => 'istpa', 'label' => 'Tes TPA?', 'type' => 'R', 'option' => mCombo::tesTPA());
	$a_kolom[] = array('kolom' => 'iswawancara', 'label' => 'Tes Wawancara?', 'type' => 'R', 'option' => mCombo::tesWawancara());
	$a_kolom[] = array('kolom' => 'iskesehatan', 'label' => 'Tes Kesehatan?', 'type' => 'R', 'option' => mCombo::tesKesehatan());
	$a_kolom[] = array('kolom' => 'ismatpel', 'label' => 'Tes Mat, IPA, dan Bhs. Inggris ?', 'type' => 'R', 'option' => mCombo::tesMapel());
	$a_kolom[] = array('kolom' => 'iskompetensi', 'label' => 'Tes Kompetensi?', 'type' => 'R', 'option' => mCombo::tesKompetensi());	
    $a_kolom[] = array('kolom' => 'lamadaftarulang', 'label' => 'Waktu daftar ulang (hari)', 'size' => 5, 'maxlength' => 5, 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Jalur';
	$p_tbwidth = 1200;
	$p_aktivitas = 'SPMB';
	
	$p_model = mJalur;
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
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
