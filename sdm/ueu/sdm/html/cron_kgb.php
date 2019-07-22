<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('kenaikan'));	
	require_once(Route::getModelPath('gaji'));
	
	$p_dbtable = 'pe_kgb';	
	$p_model = mKenaikan;
	$tglkgb = date('Y-m-d',mktime('0','0','0',(int)date('m')+1,(int)date('d'),(int)date('Y')));//bulan depan
	
	//cek apakah pegawai sudah diproses naik gaji
	$rsc = $p_model::getCekNaikGaji($conn,$tglkgb);
	
	while($rowc = $rsc->FetchRow()){
		$a_nkgb[$rowc['idpegawai']] = $rowc['idpegawai'];
	}
	
	//select pegawai yang memungkinkan bisa naik gaji
	$rs = $p_model::getPegNaikGaji($conn,$a_nkgb);
	
	while($row = $rs->FetchRow()){		
		$thn = (int) substr($row['mkglama'],0,2);
		$bln = (int) substr($row['mkglama'],2,2);
		
		$r_thn = $p_model::diffTahun($conn,$row['tmtpangkatlama'],$tglkgb);
		$r_bln = $p_model::diffBulan($conn,$row['tmtpangkatlama'],$tglkgb);
					
		$blnbaru = $bln + (int) $r_bln;
		$thnbaru = $thn + (int) $r_thn;
			
		if($blnbaru > 12){
			$thnbaru += 1;
			$blnbaru = $blnbaru % 12;
		}
		
		$gapoklama = mGaji::getGajiPokok($conn,$row['idpangkatlama'],$thn);
		$gapokbaru = mGaji::getGajiPokok($conn,$row['idpangkatlama'],$thnbaru);
		if($gapoklama < $gapokbaru){ //jika ada perubahan gaji pokok
			$record = array();
			$record['idpegawai'] = $row['idpegawai'];
			$record['tglkgb'] = $tglkgb;
			$record['pangkatlama'] = $row['idpangkatlama'];
			$record['tmtpangkatlama'] = $row['tmtpangkatlama'];
			$record['mkglama'] = $row['mkglama'];
			$record['pangkatbaru'] = $row['idpangkatlama'];
			$record['tmtpangkat'] = $tglkgb;
			$record['mkgbaru'] = str_pad($thnbaru, 2, "0", STR_PAD_LEFT).str_pad($blnbaru, 2, "0", STR_PAD_LEFT);
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
		}
	}
?>