<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//$a_auth = Modul::getFileAuth();
	
	$c_insert = true;//$a_auth['caninsert'];
	$c_edit = true;//$a_auth['canupdate'];
	$c_delete = true;//$a_auth['candelete'];
	
	$connsia = Query::connect('siunggul');
	$connsia->debug=true;
	$rs = $connsia->Execute("select * from dbo.tblDept order by Dept_ID");
	while($row = $rs->FetchRow()){
		echo $row['Dept_Name'].'<br>';
}
	// include
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mIntegrasi;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => mCombo::unitSave($conn), 'notnull' => true);
	$a_kolom[] = array('kolom' => 'kodeunitsia', 'label' => 'Unit SI Unggul', 'type' => 'S', 'option' => $p_model::getMapSIA($connsia), 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Mapping Unit SI Unggul';
	$p_tbwidth = 800;
	$p_aktivitas = 'UNIT';
	$p_dbtable = 'pe_mapunit';
	$p_key = 'idunit,kodeunitsia';
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
	if(empty($r_sort)) $r_sort = 'idunit';
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,'','',$p_dbtable);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
