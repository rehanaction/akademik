<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_paperiodebobot',true);
	
	$c_readlist = true;
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_other = $a_auth['canother'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);			
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	// properti halaman
	$p_title = 'Data Kategori Nilai';
	$p_tbwidth = 800;
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	
	$p_model = mPa;
	$p_dbtable = "pa_kategorinilai";
	$p_key = "kodeperiodebobot,kategorinilai";
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kategorinilai', 'label' => 'Kategori', 'maxlength' => 2, 'size' => 2, 'notnull' => true);
	$a_input[] = array('kolom' => 'batasbawah', 'label' => 'Batas Bawah', 'type' => 'N,2', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'batasatas', 'label' => 'Batas Atas', 'type' => 'N,2', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['kodeperiodebobot'] = $r_key;
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,'');
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$p_key);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if(!$p_posterr){
			$r_subkey  = $r_key.'|'.$record['kategorinilai'];
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$p_key);
	
	require_once(Route::getViewPath('inc_dataajax'));
?>
