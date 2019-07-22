<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getUIPath('combo'));
	
	//koneksi dengan akademik 
	$connsia = Query::connect('akad');
	if($_SERVER['REMOTE_ADDR'] == "36.85.91.184" or $_SERVER['REMOTE_ADDR'] == "66.96.234.212") //ip public sevima
		$connsia->debug=true;
	
	$p_model = mMastKepegawaian;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idtipepeg', 'label' => 'Kode Tipe Pegawai', 'size' => 2, 'maxlength' => 2, 'align' => 'center', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'tipepeg', 'label' => 'Nama Tipe Pegawai','size' => 30, 'maxlength' => 100, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'umurpensiun', 'label' => 'Umur Pensiun','size' => 5, 'maxlength' => 5, 'type' => 'N', 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Tipe Pegawai';
	$p_tbwidth = 500;
	$p_aktivitas = 'BIODATA';
	$p_dbtable = 'ms_tipepegbaru';
	$p_key = 'idtipepeg';
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST,$p_dbtable);
		
		$keyintegrasi = CStr::removeSpecial($_POST['i_'.$p_key]);
		
		// set data integrasi akademik
		$dataset = mIntegrasi::setDataIntegrasi($conn, $p_dbtable, $keyintegrasi, $p_key);
		
		// simpan data integrasi akademik
		if( empty($p_posterr))
			$p_posterr = mIntegrasi::saveDataIntegrasi($connsia, 'ms_tipepeg', $keyintegrasi, $p_key, $dataset);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key,$p_dbtable,$p_key);
		
		$keyintegrasi = CStr::removeSpecial($_POST['u_'.$p_key]);
		
		// set data integrasi akademik
		$dataset = mIntegrasi::setDataIntegrasi($conn, $p_dbtable, $keyintegrasi, $p_key);
		
		// simpan data integrasi akademik
		if( empty($p_posterr))
			$p_posterr = mIntegrasi::saveDataIntegrasi($connsia, 'ms_tipepeg', $keyintegrasi, $p_key, $dataset);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if( empty($p_posterr))
			$p_posterr = mIntegrasi::deleteDataIntegrasi($connsia, 'ms_tipepeg', $keyintegrasi, $p_key); 
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'tipepeg';
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,'','',$p_dbtable);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
