<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	//require_once(Route::getModelPath('cuti'));
	//require_once(Route::getModelPath('presensi'));	
	require_once(Route::getModelPath('integrasi'));	
		
	//$p_model = mCuti;
	
	$conn->debug = true;
	
	/*$sql = "select max(tglcuti) as tgl,idpegawai from sdm.cutitemp 
			where ltrim(rtrim(jeniscuti))='Cuti Melahirkan' and idpegawai is not null group by idpegawai";
	$rs = $conn->Execute($sql);
	
	while ($row = $rs->FetchRow()){
		//$record = array();
		//$record['idpegawa']
		$conn->Execute("update sdm.cutitemp set tglselesai='$row[tgl]' where idpegawai=$row[idpegawai] and ltrim(rtrim(jeniscuti))='Cuti Melahirkan'");
	}*/
	
	/*$sql = "select nourutcuti,idpegawai from sdm.pe_rwtcuti where t_username='migrasi' and idjeniscuti='C'";
	$rs = $conn->Execute($sql);
	
	$sqlinsert = '';
	while ($row = $rs->FetchRow()){
		$sqlstr = "select tglcuti from sdm.cutitemp 
					where idpegawai=$row[idpegawai] and ltrim(rtrim(jeniscuti))='Cuti Tahunan'";
		$rss = $conn->Execute($sqlstr);
		while($col = $rss->FetchRow()){
			$record = array();
			$record['nourutcuti'] = $row['nourutcuti'];
			$record['tglmulai'] = $col['tglcuti'];
			$record['tglselesai'] = $col['tglcuti'];
			$record['t_username'] = 'migrasi';
			
			//list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'pe_rwtcutidet');		
			//Query::recInsert($conn,$record,'sdm.pe_rwtcutidet');
			$col = $conn->SelectLimit("select * from sdm.pe_rwtcutidet",1);
			$sql = $conn->GetInsertSQL($col,$record);
			//$sqlinsert .= $sql.';<br />';
			$conn->Execute($sql);
		}
	}
	echo $sqlinsert;*/

	/*$sql = "select nourutcuti,idpegawai from sdm.pe_rwtcuti where t_username='migrasi' and idjeniscuti='CH'";
	$rs = $conn->Execute($sql);
	
	$sqlinsert = '';
	while ($row = $rs->FetchRow()){
		$sqlstr = "select idpegawai,tglpengajuan,tglselesai from sdm.cutitemp where idpegawai=$row[idpegawai] and ltrim(rtrim(jeniscuti))='Cuti Melahirkan'
			group by idpegawai,tglpengajuan,tglselesai";
		$rss = $conn->Execute($sqlstr);
		while($col = $rss->FetchRow()){
			$record = array();
			$record['nourutcuti'] = $row['nourutcuti'];
			$record['tglmulai'] = $col['tglpengajuan'];
			$record['tglselesai'] = $col['tglselesai'];
			$record['t_username'] = 'migrasi';
			
			//list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'pe_rwtcutidet');		
			//Query::recInsert($conn,$record,'sdm.pe_rwtcutidet');
			$col = $conn->SelectLimit("select * from sdm.pe_rwtcutidet",1);
			$sql = $conn->GetInsertSQL($col,$record);
			//$sqlinsert .= $sql.';<br />';
			$conn->Execute($sql);
		}
	}*/

	
	/*$sqlupdate = '';
	$sql = "select tglmulai,tglselesai,nocutidet from sdm.pe_rwtcutidet where t_username='migrasi' and lamacuti is null ";
	$rs = $conn->Execute($sql);
	
	while ($row = $rs->FetchRow()){
		$record['lamacuti'] = mCuti::getLamaCuti($conn,$row['tglmulai'],$row['tglselesai']);
		
		$col = $conn->Execute("select * from sdm.pe_rwtcutidet where nocutidet=$row[nocutidet]");
		$sql = $conn->GetUpdateSQL($col,$record);
		if($sql != '')
			$conn->Execute($sql);
			//$sqlupdate .= $sql.';<br />';
	}
	//echo $sqlupdate;*/
	
	$sql = "select idpegawai from sdm.ms_pegawai where nik is not null";
	$rs = $conn->Execute($sql);
	
	while ($row = $rs->FetchRow()){
		mIntegrasi::savePejabatRole($conn,$row['idpegawai']);
	}
?>