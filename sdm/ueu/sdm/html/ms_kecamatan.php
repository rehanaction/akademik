<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastpelengkap'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mMastPelengkap;
	$p_dbtable = 'lv_kecamatan';
	$p_key = 'idkecamatan';
	
	// variabel request
	$r_propinsi = Modul::setRequest($_POST['propinsi'],'PROPINSI');
	$r_kabupaten = Modul::setRequest($_POST['kabupaten'],'KABUPATEN');
	
	// combo
	$l_propinsi = uCombo::propinsi($conn,$r_propinsi,'propinsi','onchange="goSubmit()" style="width:228px"',false);
	$l_kabupaten = uCombo::kabupaten($conn,$r_kabupaten,'kabupaten','onchange="goSubmit()" style="width:228px"',false,"where substring(idkabupaten,1,2) = '$r_propinsi'");
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idkecamatan', 'label' => 'Kode', 'size' => 6, 'maxlength' => 6, 'align' => 'center', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'namakecamatan', 'label' => 'Nama Kecamatan', 'size' => 40, 'maxlength' => 50, 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Kecamatan';
	$p_tbwidth = 400;
	$p_aktivitas = 'WILAYAH';
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST,$p_dbtable);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'namakecamatan';
	
	// mendapatkan data
	if(!empty($r_propinsi) and !empty($r_kabupaten))
		$a_filter[] = $p_model::getListFilter('kabupaten',$r_kabupaten);
	else
		$a_filter[] = " 1=0";
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter,'',$p_dbtable);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Propinsi', 'combo' => $l_propinsi);
	$a_filtercombo[] = array('label' => 'Kabupaten', 'combo' => $l_kabupaten);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
