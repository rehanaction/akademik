<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$p_detailpage = "data_tarif";
	// include
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tarif'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur = Modul::setRequest($_POST['jalurpenerimaan'],'JALUR');
	
	$arr_flag = mCombo::arrFlagtagihan();
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','onchange="goSubmit()"',true);
		
        
	// properti halaman
	$p_title = 'Tarif Pembayaran Rutin';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Master';
	
	$p_model = mJenistagihan;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	
	//daftar jenis tagihan
		$arr_jenistagihan = mJenistagihan::getArray($conn,array('A','B','S','T'));
		
	// daftar jurusan
		$arr_unit = mAkademik::getArrayunit($conn,false,'3');
		
	//daftar sistem kuliah 
		$arr_sistemkuliah = mAkademik::getArraysistemkuliah($conn);
		
	//data tarif
		$arr_tarif = mTarif::getArraytarif($conn,$r_periode,$r_jalur);
		if($arr_tarif)
		foreach($arr_tarif as $i => $val){
				$data[$val['kodeunit']][$val['sistemkuliah']][$val['jenistagihan']] = $val['nominaltarif'];
			}
		
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jalur Penerimaan', 'combo' => $l_jalur);
	
	require_once($conf['view_dir'].'v_list_tarif.php');
?>