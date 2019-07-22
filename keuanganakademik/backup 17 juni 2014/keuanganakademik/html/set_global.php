<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('jenistagihan'));
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
	$arr_tagihan = mJenistagihan::getArray($conn);
	
	$arr_periode = mAkademik::getArrayperiode($conn);
	$arr_periodeyudisium = mAkademik::getArrayperiodeyudisium($conn);
	
	
	$arr_bulan = Date::arrayMonth(false);
	for($i = date('Y')-10;$i<= date('Y')+20;$i++ ){
		$arr_tahun[$i] = $i;
		}
		
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
				
				$rec = array();
				$rec['jenistagihan'] = 'FRM';
				$rec['idsetting'] = '1';
				$rec['periodesekarang'] = cStr::cStrNull($_POST['periode_FRM']);
				$rec['allow_inquiry'] =  cStr::cStrNull($_POST['inq_FRM']);
				$rec['allow_payment'] =  cStr::cStrNull($_POST['pay_FRM']);
				$rec['allow_reversal'] =  cStr::cStrNull($_POST['rev_FRM']);
				
				$r_keydetail = $_POST['key_FRM'];
				if($r_keydetail)
					$err = mSettingh2hdetail::updateRecord($conn,$rec,$r_keydetail);
				else
					$err = mSettingh2hdetail::insertRecord($conn,$rec,$r_keydetail);
				
			
			foreach($arr_tagihan as $i => $v){
				$rec = array();
				$rec['jenistagihan'] = $v['jenistagihan'];
				$rec['idsetting'] = '1';
				$rec['periodesekarang'] = cStr::cStrNull($_POST['periode_'.$v['jenistagihan']]);
				
				if($_POST['bulan_'.$v['jenistagihan']])
					$bulan = str_pad($_POST['bulan_'.$v['jenistagihan']],2,'0',STR_PAD_LEFT);
				if($_POST['tahun_'.$v['jenistagihan']])
					$tahun = $_POST['tahun_'.$v['jenistagihan']];
					
				if($bulan and $tahun)
					$rec['bulantahunsekarang'] =  $tahun.$bulan;
				
				$rec['allow_inquiry'] =  cStr::cStrNull($_POST['inq_'.$v['jenistagihan']]);
				$rec['allow_payment'] =  cStr::cStrNull($_POST['pay_'.$v['jenistagihan']]);
				$rec['allow_reversal'] =  cStr::cStrNull($_POST['rev_'.$v['jenistagihan']]);
				
				$r_keydetail = $_POST['key_'.$v['jenistagihan']];
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