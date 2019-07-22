<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$p_tbwidth = "500";
	$p_title = "Data Jenis Potongan";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ms_potongan";
	$p_key = "kodepotongan";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	if(empty($r_key))
		$a_input[] = array('kolom' => 'kodepotongan', 'label' => 'Kode', 'maxlength' => 6, 'size' => 6, 'notnull' => true);
	else
		$a_input[] = array('kolom' => 'kodepotongan', 'label' => 'Kode', 'maxlength' => 6, 'size' => 6, 'readonly' => true);
	$a_input[] = array('kolom' => 'namapotongan', 'label' => 'Potongan', 'maxlength' => 100, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'ismanual', 'label' => 'Cara Hitung', 'type' => 'R', 'option' => $p_model::caraHitungPot());
	$a_input[] = array('kolom' => 'norekening', 'label' => 'Pos No. Rekening', 'size' => 30, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'anrekening', 'label' => 'An. Rekening', 'size' => 40, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif', 'type' => 'C', 'option' => array("Y" => "Aktif"));
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			if(empty($r_key))
				$r_key = $record['kodepotongan'];
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	require_once(Route::getViewPath('inc_data'));
?>