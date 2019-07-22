<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('pekerjaan'));	
	
	$p_dbtable = 'pe_pensiun';	
	$p_model = mPekerjaan;
	$tmtpensiun = date('Y-m-d');
	
	//cek apakah pegawai sudah diproses pensiun
	$rsc = $p_model::getCekPensiun($conn,$tmtpensiun);
	
	while($rowc = $rsc->FetchRow()){
		$a_nps[$rowc['idpegawai']] = $rowc['idpegawai'];
	}
	
	//select pegawai yang memungkinkan bisa naik pangkat
	$rs = $p_model::getPegPensiun($conn,$tmtpensiun,$a_nps);
	
	while($row = $rs->FetchRow()){	
		$record = array();
		$record['idpegawai'] = $row['idpegawai'];
		$record['periodepensiun'] = substr($row['tmtpensiun'],0,4).substr($row['tmtpensiun'],5,2);	
		$record['tmtpensiun'] = $row['tmtpensiun'];
		list($mthn,$mbln) = explode(':',$row['masakerja']);
		$record['masakerjathn'] = $mthn;
		$record['masakerjabln'] = $mbln;
		$record['idstatusaktif'] = 'PN';//pensiun normal
	
		list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
	}
?>