<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('settingprodi'));
	require_once(Route::getUIPath('combo'));
	
	$a_unit = mCombo::jurusan($conn);
	$a_sistemkuliah = mCombo::sistemkuliah($conn);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Prodi', 'type'=>'S', 'option'=>$a_unit, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'sistemkuliah', 'type'=>'S','option'=>$a_sistemkuliah, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'isbuka', 'label' => 'Buka ?', 'type' => 'C', 'option' => array('-1' => ''));
	
    $t_kolom = count($a_kolom);
		
	// properti halaman
	$p_title = 'Setting Pembukaan Prodi';
	$p_tbwidth = 500;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = mSettingProdi;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+1;
	
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
