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
	
	// variabel request
	$r_tipe = Modul::setRequest($_POST['idtipepeg'],'TIPEPEG');
	
	// combo
	$l_tipe = uCombo::tipepegawaibaru($conn,$r_tipe,'idtipepeg','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Daftar Jenis Pegawai';
	$p_tbwidth = 500;
	$p_aktivitas = 'BIODATA';
	$p_dbtable = 'ms_jenispegbaru';
	$p_key = 'idjenispegawai';
	
	// properti integrasi akademik
	$addcondition = array('koderole' => array('D' => 'D', 'default' => 'PPA'), 'jenispengawas' => array('D' => 'D', 'default' => 'P'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idjenispegawai', 'label' => 'Kode', 'size' => 5, 'maxlength' => 5, 'align' => 'center', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'jenispegawai', 'label' => 'Nama Jenis Pegawai', 'size' => 40, 'maxlength' => 100, 'notnull' => true);
	
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan tipe pegawai
		$a_kolom[] = array('kolom' => 'idtipepeg', 'value' => $r_tipe);
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST,$p_dbtable);
		
		// buang lagi tipepegawai
		array_pop($a_kolom);
		
		$keyintegrasi = CStr::removeSpecial($_POST['i_'.$p_key]);
		
		// set data integrasi akademik
		$addset = array('koderole' => CStr::removeSpecial($_POST['idtipepeg']), 'jenispengawas' => CStr::removeSpecial($_POST['idtipepeg']));
		$dataset = mIntegrasi::setDataIntegrasi($conn, $p_dbtable, $keyintegrasi, $p_key, $addset, $addcondition);
		
		// simpan data integrasi akademik
		if( empty($p_posterr))
			$p_posterr = mIntegrasi::saveDataIntegrasi($connsia, 'ms_jenispeg', $keyintegrasi, $p_key, $dataset);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key,$p_dbtable,$p_key);
		
		$keyintegrasi = CStr::removeSpecial($_POST['u_'.$p_key]);
		
		// set data integrasi akademik
		$addset = array('koderole' => CStr::removeSpecial($_POST['idtipepeg']), 'jenispengawas' => CStr::removeSpecial($_POST['idtipepeg']));
		$dataset = mIntegrasi::setDataIntegrasi($conn, $p_dbtable, $keyintegrasi, $p_key, $addset, $addcondition);
		
		// simpan data integrasi akademik
		if( empty($p_posterr))
			$p_posterr = mIntegrasi::saveDataIntegrasi($connsia, 'ms_jenispeg', $keyintegrasi, $p_key, $dataset);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if( empty($p_posterr))
			$p_posterr = mIntegrasi::deleteDataIntegrasi($connsia, 'ms_jenispeg', $keyintegrasi, $p_key); 
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'jenispegawai';
	
	// mendapatkan data
	if(!empty($r_tipe)) $a_filter[] = $p_model::getListFilter('idtipepeg',$r_tipe);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter,'',$p_dbtable);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Tipe Pegawai', 'combo' => $l_tipe);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
