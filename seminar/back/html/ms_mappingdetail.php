<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth($conng);
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('imporpendaftar'));
	
	$p_model = mMappingDetail;
	$p_key = $p_model::key;
	
	// variabel request
	$r_mapping = Modul::setRequest($_POST['mapping'],'MAPPING');
	
	// combo
	$a_mapping = mMapping::getArray($conn);
	if(empty($a_mapping[$r_mapping]))
		$r_mapping = key($a_mapping);
		
	$l_mapping = UI::createSelect('mapping',$a_mapping,$r_mapping,'ControlStyle',true,'onchange="goSubmit()"');
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'datasumber', 'label' => 'Asal', 'size' => 30, 'maxlength' => 100, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'datatujuan', 'label' => 'Menjadi', 'size' => 30, 'maxlength' => 100, 'notnull' => true);
	
	// properti halaman
	$p_title = 'Detail Mapping Data';
	$p_tbwidth = 500;
	$p_aktivitas = 'TABEL';
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan mapping
		$a_kolom[] = array('kolom' => 'kodemapping', 'value' => $r_mapping);
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
		// buang lagi mapping
		array_pop($a_kolom);
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
	$a_filter[] = $p_model::getListFilter('mapping',$r_mapping);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Mapping', 'combo' => $l_mapping);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>