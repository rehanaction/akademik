<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
       // hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	
        
	// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tarifformulir'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_self = (int)$_REQUEST['self'];
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getUserName();
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
		
	$arr_key = explode('|',$r_key);
	$r_periodedaftar = $arr_key[0];
	$r_jalur = $arr_key[1];
	$r_gelombang = $arr_key[2];
	
	// properti halaman
	$p_title = 'Tarif Formulir';
	$p_tbwidth = 600;
	$p_aktivitas = 'Master';
	$p_listpage = Route::getListPage();
	
	$p_model = mTarifformulir;
	
	
        // hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
        
	// struktur view
	if(empty($r_key))
		$p_edit = false;
	else
		$p_edit = true;
	//daftar sistem kuliah 
		$arr_sistemkuliah = mAkademik::getArraysistemkuliah($conn);    
	//program pendidikan
		$arr_programpend = mAkademik::getProgrampend($conn);    
		
		
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','',true,false);
	$l_gelombang = uCombo::gelombang($conn,$r_gelombang,'idgelombang','',true,false);
      
       //aksi
        $r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		
		if($arr_programpend)
		foreach($arr_programpend as $programpend => $vp){
			if($arr_sistemkuliah)
			foreach($arr_sistemkuliah as $i => $vs){
					for($i = 1; $i <= 3; $i++){
							$record = array();
							$record['periodedaftar'] = $r_periodedaftar;
							$record['idgelombang'] = $r_gelombang;
							$record['jalurpenerimaan'] = $r_jalur;
							$record['sistemkuliah'] = $vs['sistemkuliah'];
							$record['programpend'] = $programpend;
							$record['jumlahpilihan'] = $i;
							//cek apakah sudah ada
							$key = mTarifformulir::getIdtarif($conn,$record);
							
							$kode = $programpend.'|'.$vs['sistemkuliah'].'|'.$i;
							$record['nominaltarif'] = cStr::cStrDec($_POST['tarif|'.$kode]);
							$record['kodeformulir'] = cStr::cStrNull($_POST['kode|'.$kode]);
							
							if($record['kodeformulir'] == 'null' or empty($_POST['aktif|'.$kode]))
								$record['isaktif'] = 0;
							else
								$record['isaktif'] = 1;
							
							if(empty($key))
								list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,$key);
							else
								list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$key);
						}
				}
			}
		
		if(!$p_posterr) unset($post);
	}
	
	$arr_tarif = mTarifformulir::getArraytarif($conn,$r_periodedaftar,$r_jalur,$r_gelombang);
	if($arr_tarif)
		foreach($arr_tarif as $i => $v){
			$data[$v['periodedaftar']][$v['jalurpenerimaan']][$v['idgelombang']][trim($v['sistemkuliah'])][$v['jumlahpilihan']][$v['programpend']] = $v;
			}
	require_once($conf['view_dir'].'v_data_tariffrm.php');
        
?>
