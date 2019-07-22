<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('presensi'));	
	
	$p_dbtable = 'pe_presensidet';
	$p_key = 'tglpresensi,idpegawai';
	
	$p_model = mPresensi;
	
	$a_ahari = array();
	$a_ahari = $p_model::getDayPresensi();
	$a_hari = array();
	$a_hari = $a_ahari['aday'];
	
	$a_jamhadir = array();
	$a_jamhadir = $p_model::getJamHadir($conn);
	
	$a_presensidate = array();
	$a_presensidate = $p_model::getPresensiDate($conn, date("Y-m-d"));
	
	$a_data = $p_model::getListCronPresensi($conn);
	
	if (count($a_data) > 0){
		foreach($a_data as $row){
			$record = array();
			$record['idpegawai'] = $row['idpegawai'];
			$record['tglpresensi'] = $row['tglsekarang'];
			$record['tglpemasukkan'] = $row['tglsekarang'];
			$record['sjamdatang'] = $a_jamhadir['jamdatang'][$row[$a_hari[$row['nohari']]]];
			$record['sjampulang'] = $a_jamhadir['jampulang'][$row[$a_hari[$row['nohari']]]];
			$r_key = date("Y-m-d").'|'.$record['idpegawai'];
			
			if ($a_presensidate['idpegawai'][$row['idpegawai']] == $row['idpegawai']) //update
				list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true,$p_dbtable,$p_key);		
			else{ //insert
				$islibur = $p_model::isDataExist($conn, date("Y-m-d"), 'ms_liburdetail', 'tgllibur');
				if (!$islibur){
					$record['kodeabsensi'] = 'A';
					if (empty($record['sjamdatang']))
						$record['kodeabsensi'] = 'L';
				}else
					$record['kodeabsensi'] = 'L';
				list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);		
			}
		}
	}
?>