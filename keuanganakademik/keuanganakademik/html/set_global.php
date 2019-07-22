<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('kelompoktagihan'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('settingh2h'));
	require_once(Route::getModelPath('settingh2hdetail'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Setting Global';
	$p_tbwidth = 980;
	$p_aktivitas = 'SETTING';
	
	$p_model = mSettingh2h;	
	$arr_tagihan = mKelompokTagihan::arrQuery($conn);
	
	$arr_periode = mAkademik::getArrayperiode($conn);
	$arr_periodeyudisium = mAkademik::getArrayperiodeyudisium($conn);
	
	$arr_bulan = Date::arrayMonth(false);
	for($i = date('Y')-10;$i<= date('Y')+20;$i++ )
		$arr_tahun[$i] = $i;
		
	$arr_periodedaftar = mAkademik::getPeriodedaftar($conn);
	
	$r_act = $_POST['act'];
	$r_key = $_POST['key'];
	if($r_act == 'setGlobal' and $c_edit){
		$record = array();
		$record['reversal_time'] = $_POST['reversal_time'];
		$record['idsetting'] = '1';
		if($r_key)
			$err = mSettingh2h::updateRecord($conn,$record,$r_key);
		else
			$err = mSettingh2h::insertRecord($conn,$record,$r_key);
		
		foreach($arr_tagihan as $k => $v){
			$rec = array();
			$rec['jenistagihan'] = $k;
			$rec['idsetting'] = '1';
			$rec['periodesekarang'] = cStr::cStrNull($_POST['periode_'.$k]);
			
			$rec['allow_inquiry'] =  cStr::cStrNull($_POST['inq_'.$k]);
			$rec['allow_payment'] =  cStr::cStrNull($_POST['pay_'.$k]);
			$rec['allow_reversal'] =  cStr::cStrNull($_POST['rev_'.$k]);
			
			$r_keydetail = $_POST['key_'.$k];
			if($r_keydetail)
				$err = mSettingh2hdetail::updateRecord($conn,$rec,$r_keydetail);
			else
				$err = mSettingh2hdetail::insertRecord($conn,$rec,$r_keydetail);
		}
	}
	
	$data = mSettingh2h::getDataSetting($conn);
	$datadetail = mSettingh2hdetail::getDataSetting($conn);
	
	require_once(Route::getViewPath('v_set_global'));
?>
