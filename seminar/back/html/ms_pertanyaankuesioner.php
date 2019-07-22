<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	ini_set('display_errors', true);
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pertanyaankuesioner'));
	require_once(Route::getModelPath('kategorijawaban'));
	require_once(Route::getUIPath('combo'));
	
	$periode 	= mCombo::periode($conn);
	
	//$aktif = array('0'=> '0', '1' => '1');
	//$a_pilihan = mKategoriKuisioner::getArray($conn);	
	//$a_pertanyaan = mPertanyaanKuesioner::getPertanyaan($conn,1);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idpertanyaan', 'label' => 'ID', 'size' => 2, 'maxlength' => 2, 'required' => true);
	$a_kolom[] = array('kolom' => 'pertanyaan', 'label' => 'Pertanyaan',  'type' => 'A', 'cols' => 50, 'required' => true);
	//$a_kolom[] = array('kolom' => 'kategori', 'label' => 'Kategori', 'type' => 'S', 'option' => $a_pilihan, 'empty' => false);
	$a_kolom[] = array('kolom' => 'isaktif', 'label' => 'Aktif', 'notnull' => true, 'type' => 'C', 'option' => array('1' => '0'));
	
	
	// properti halaman
	$p_title = 'Daftar Pertanyaan Kuesioner';
	$p_tbwidth = 700;
	$p_model = mPertanyaanKuesioner;
	$p_key = $p_model::key;
	
	/* filter atas
	$a_periode = mPeriode::getListCombo($conn);
	$r_periode = Page::setRequest($_POST['periode'],'PERIODE',$a_periode);
	Page::addPagerFilter('periode',$r_periode,$p_model);
	
	$a_filterhead = array();
	$a_filterhead[] = array('Periode Akademik',UI::createSelect('periode',$a_periode,$r_periode,'form-control input-sm',true,'onchange="goSubmit(this)"'));
	*/
	
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