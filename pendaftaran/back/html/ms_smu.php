<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	/*
	//hapus setelah diintegrasikan
	$c_insert = true;
	$c_edit = true;
	$c_delete = true;
	*/
	// include
	require_once(Route::getModelPath('smu'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Daftar Sekolah Menengah';
	$p_tbwidth = 900;
	$p_aktivitas = 'UNIT';
	
	$p_model = mSmu;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	//getkotauntuk optionnya
	$kota=$p_model::getKota($conn);
	//$kota=array_values($kota);
	// variabel request
	$r_propinsi = Modul::setRequest($_POST['propinsi'],'PROPINSI');
	$r_kota = Modul::setRequest($_POST['kota'],'KOTA');
	
	// combo
	$l_propinsi = uCombo::propinsi($conn,$r_propinsi,'','propinsi','onchange="goSubmit()"');
	$l_kota = uCombo::kota($conn,$r_kota,'','kota','onchange="goSubmit()"',true,$r_propinsi);
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namasmu', 'label' => 'SMU', 'size' => 20, 'maxlength' => 20, 'notnull' => true);
        $a_kolom[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat', 'size' => 2, 'maxlength' => 9, 'notnull' => true, 'type'=>'A');
        $a_kolom[] = array('kolom' => 'telpsmu', 'label' => 'Telp', 'size' => 15, 'maxlength' => 13, 'notnull' => true);
        $a_kolom[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'notnull' => true, 'type'=>'S', 'option' => $kota);
        
	
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
	if(!empty($r_propinsi)) $a_filter[] = $p_model::getListFilter('propinsi',$r_propinsi);
	if(!empty($r_kota)) $a_filter[] = $p_model::getListFilter('kota',$r_kota);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Propinsi', 'combo' => $l_propinsi);
	$a_filtercombo[] = array('label' => 'Kota', 'combo' => $l_kota);
	require_once($conf['view_dir'].'inc_ms.php');
?>

