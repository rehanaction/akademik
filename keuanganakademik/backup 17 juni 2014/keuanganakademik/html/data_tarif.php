<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tarif'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$arr_key = explode('|',$r_key);
	$r_periode = $arr_key[1];
	$r_kodeunit = $arr_key[0];
	$r_jalur = $arr_key[2];
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','',true,false);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','',true,false);
	$l_unit = uCombo::unit($conn,$r_kodeunit,'kodeunit','',true,false);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Tarif';
	$p_tbwidth = 600;
	$p_aktivitas = 'KEUANGAN';
	$p_listpage = Route::getListPage();
	
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
			
	//daftar jenis tagihan
		$arr_jenistagihan = mJenistagihan::getArray($conn,array('A','B','S','T'));
		
	//daftar sistem kuliah 
		$arr_sistemkuliah = mAkademik::getArraysistemkuliah($conn);
	
	//penyimpanan
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit){
		$conn->BeginTrans();
		$err = 0;
			if($arr_sistemkuliah)
			foreach($arr_sistemkuliah as $i => $val){
				if($arr_jenistagihan)
				foreach($arr_jenistagihan as $j => $v){
				
						$record = array();
						$record['kodeunit'] = $r_kodeunit;
						$record['jalurpenerimaan'] = $r_jalur;
						$record['periodetarif'] = $r_periode;
						$record['jenistagihan'] = $v['jenistagihan'];
						$record['sistemkuliah'] = $val['sistemkuliah'];
				$idtarif = mTarif::getIdtarif($conn,$record);
				$record['nominaltarif'] = cStr::cStrDec($_POST[$val['sistemkuliah'].'|'.$v['jenistagihan']]);
				if($err==0){
					if(!$idtarif)
						$err = mTarif::insertRecord($conn,$record);
					else
						$err = mTarif::updateRecord($conn,$record,$idtarif); 
				}
					}
				}
				$p_posterr = $err;
				if($err==0)
						$p_postmsg = "Penyimpanan Tarif Berhasil"; 
				else
						$p_postmsg = "Penyimpanan Tarif Gagal";
				
		$conn->CommitTrans();
		}if($r_act == 'delete' and $c_delete){
			$record = array();
			$record['kodeunit'] = $r_kodeunit;
			$record['jalurpenerimaan'] = $r_jalur;
			$record['periodetarif'] = $r_periode;
			$err = mTarif::delete($conn,$record);
			$p_posterr = $err;
			if($err==0)
						$p_postmsg = "Penyimpanan Tarif Berhasil"; 
				else
						$p_postmsg = "Penyimpanan Tarif Gagal";
		}
	
	
	//data tarif
		$arr_tarif = mTarif::getArraytarif($conn,$r_periode,$r_jalur,$r_kodeunit);
		if($arr_tarif)
		foreach($arr_tarif as $i => $val){
				$data[$val['kodeunit']][$val['sistemkuliah']][$val['jenistagihan']] = $val['nominaltarif'];
			}
	
	require_once(Route::getViewPath('v_data_tarif'));
?>