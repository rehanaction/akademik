<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('kenaikan'));	
	
	$p_dbtable = 'pe_kpb';	
	$p_model = mKenaikan;
	$tglkpb = date('Y-m-d');
	
	//cek apakah pegawai sudah diproses naik pangkat
	$rsc = $p_model::getCekNaikPangkat($conn,$tglkpb);
	
	while($rowc = $rsc->FetchRow()){
		$a_nkpb[$rowc['idpegawai']] = $rowc['idpegawai'];
	}
	
	//select pegawai yang memungkinkan bisa naik pangkat
	$rs = $p_model::getPegNaikPangkat($conn,$tglkpb,$a_nkpb);
	
	while($row = $rs->FetchRow()){		
		$record = array();
		$record['idpegawai'] = $row['idpegawai'];
		$record['tglkpb'] = $tglkpb;
		$record['idpangkatlama'] = $row['idpangkat'];
		$record['tmtpangkatlama'] = $row['tmtpangkat'];
		$record['mkglama'] = str_pad($row['masakerjathngol'], 2, "0", STR_PAD_LEFT).str_pad($row['masakerjablngol'], 2, "0", STR_PAD_LEFT);
		
		//mendapatkan next pangkat
		$recordn = $p_model::getNextPangkat($conn,$row['idpegawai'],$tglkpb);
		
		//penggabungan array
		$record = array_merge($record,$recordn);
		
		list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
	}
?>