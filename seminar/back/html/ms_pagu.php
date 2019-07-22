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
	require_once(Route::getModelPath('periode'));
        require_once(Route::getModelPath('jalur'));
        require_once(Route::getModelPath('gelombang'));
        require_once(Route::getModelPath('pagu'));
	require_once(Route::getUIPath('combo'));
	
        // properti halaman
	$p_title = 'Daftar Pagu Jurusan Untuk Jalur dan Gelombang Aktif';
	$p_tbwidth = 800;
	$p_aktivitas = 'UNIT';
	
	$p_model = mPagu;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
     
    $r_jalur 	= Modul::setRequest($_POST['jalur'],'Jalur Penerimaan');
    $l_jalur 	= uCombo::jalur($conn,$r_jalur,'','jalur','onchange="goSubmit()"');   
        //get optionnya
	$jurusan=$p_model::getunit($conn);
	//$jurusan=array_values($jurusan);
        
        $periode=mPeriode::getPeriode($conn);
        $jalur=mJalur::getAllJalur($conn);
        $gelombang=mGelombang::getGelombang($conn);
	
        
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Jurusan', 'size' => 2, 'maxlength' => 4, 'notnull' => true,'type'=>'S', 'option' => $jurusan);
        $a_kolom[] = array('kolom' => 'periodedaftar', 'label' => 'Periode', 'size' => 2, 'maxlength' => 4, 'type'=>'S', 'option' => $periode);
        $a_kolom[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur Penerimaan', 'size' => 2, 'maxlength' => 4, 'type'=>'S', 'option' => $jalur);
        $a_kolom[] = array('kolom' => 'idgelombang', 'label' => 'Gelombang', 'size' => 2, 'maxlength' => 4, 'type'=>'S', 'option' => $gelombang);
        $a_kolom[] = array('kolom' => 'pagu', 'label' => 'Pagu', 'size' => 2, 'maxlength' => 4, 'notnull' => true);
	
	
	
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
	if(!empty($r_jalur)) $a_filter[] = $p_model::getListFilter('jalur',$r_jalur);
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Jalur Penerimaan', 'combo' => $l_jalur);
	require_once($conf['view_dir'].'inc_ms.php');
?>
