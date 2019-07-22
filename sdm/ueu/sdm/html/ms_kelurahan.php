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
	$p_dbtable = 'lv_kelurahan';
	$p_key = 'idkelurahan';
	
	// variabel request
	$r_propinsi = Modul::setRequest($_POST['propinsi'],'PROPINSI');
	$r_kabupaten = Modul::setRequest($_POST['kabupaten'],'KABUPATEN');
	$r_kecamatan = Modul::setRequest($_POST['kecamatan'],'KECAMATAN');
	
	// combo
	$l_propinsi = uCombo::propinsi($conn,$r_propinsi,'propinsi','onchange="goSubmit()" style="width:228px"',false);
	$l_kabupaten = uCombo::kabupaten($conn,$r_kabupaten,'kabupaten','onchange="goSubmit()" style="width:228px"',false,"where substring(idkabupaten,1,2) = '$r_propinsi'");
	$l_kecamatan = uCombo::kecamatan($conn,$r_kecamatan,'kecamatan','onchange="goSubmit()" style="width:228px"',false,"where substring(idkecamatan,1,4) = '$r_kabupaten'");
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idkelurahan', 'label' => 'Kode', 'size' => 8, 'maxlength' => 8, 'align' => 'center', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'namakelurahan', 'label' => 'Nama Kelurahan', 'size' => 40, 'maxlength' => 50, 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Kelurahan';
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
	if(empty($r_sort)) $r_sort = 'namakelurahan';
	
	// mendapatkan data
	if(!empty($r_propinsi) and !empty($r_kabupaten) and !empty($r_kecamatan))
		$a_filter[] = $p_model::getListFilter('kecamatan',$r_kecamatan);
	else
		$a_filter[] = " 1=0";
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter,'',$p_dbtable);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Propinsi', 'combo' => $l_propinsi);
	$a_filtercombo[] = array('label' => 'Kabupaten', 'combo' => $l_kabupaten);
	$a_filtercombo[] = array('label' => 'Kecamatan', 'combo' => $l_kecamatan);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
