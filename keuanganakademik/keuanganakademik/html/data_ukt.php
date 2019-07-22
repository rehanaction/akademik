<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('ukt'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('akademik')); 
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'UKT';
	$p_tbwidth = 600;
	$p_aktivitas = 'KEUANGAN';
	$p_listpage = Route::getListPage();
	$p_model = mUkt;
	
	$arr_periode = mCombo::periode($conn);
	$arr_ukt = mCombo::kategoriukt($conn);
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
	$p_listpage = "list_tarifukt";
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Kodeunit', 'type' => 'S','option' => mAkademik::getArrayunit($conn));
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode','type' => 'S','option' => $arr_periode);
	$a_input[] = array('kolom' => 'kodekategoriukt', 'label' => 'Kategori UKT', 'type' => 'S','option' => $arr_ukt);
	$a_input[] = array('kolom' => 'nilaitarif', 'label' => 'Tarif');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan');
	
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
				$r_key=$record['periode'].'|'.$record['kodeunit'].'|'.$record['kodekategoriukt'];
				}
		else{
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
				$r_key=$record['periode'].'|'.$record['kodeunit'].'|'.$record['kodekategoriukt'];

			}
		
		if(!$p_posterr) {
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);		
	
	require_once(Route::getViewPath('v_data_ukt'));
?>