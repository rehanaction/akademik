<?php
	ob_clean();
	
	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	$c_periode = $_POST["tahun"].$_POST['semester'];
	$conn->debug = false;
	
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	if($filter<>'all_dbf') {
		header("Content-Type: dbf");
		header('Content-Disposition: attachment; filename="TBKMK.DBF"');
	}
		
	$strSQL = "select * from epsbed.tbkmk where tahunpelaporan='$c_thnpelaporan' and thsmstbkmk='$c_periode' and kdpsttbkmk='$rs_kode' order by idtbkmk asc";		
	$rs = $conn->Execute($strSQL);	
		
	$filedbf = "file_dbf/TBKMK.DBF";
	$newfile = "file_dbf/copy/TBKMK.DBF";
	copy($filedbf, $newfile);

	$db = dbase_open($newfile,2);
	
	if ($db) {
	while ($row = $rs->FetchRow()) {
		if($row["tahunpelaporan"])
			$tahunpelaporan = $row["tahunpelaporan"];
		else
			$tahunpelaporan = "  ";
			
		if($row["thsmstbkmk"])
			$thsmstbkmk = $row["thsmstbkmk"];
		else
			$thsmstbkmk = "  ";

		if($row["kdptitbkmk"])
			$kdptitbkmk = $row["kdptitbkmk"];
		else
			$kdptitbkmk = "  ";

		if($row["kdpsttbkmk"])
			$kdpsttbkmk = $row["kdpsttbkmk"];
		else
			$kdpsttbkmk = "  ";

		if($row["kdjentbkmk"])
			$kdjentbkmk = $row["kdjentbkmk"];
		else
			$kdjentbkmk = "  ";

		if($row["kdkmktbkmk"])
			$kdkmktbkmk = $row["kdkmktbkmk"];
		else
			$kdkmktbkmk = "  ";	

		if($row["nakmktbkmk"])
			$nakmktbkmk = $row["nakmktbkmk"];
		else
			$nakmktbkmk = "  ";

		if($row["sksmktbkmk"])
			$sksmktbkmk = $row["sksmktbkmk"];
		else
			$sksmktbkmk = 0;

		if($row["skstmtbkmk"])
			$skstmtbkmk = $row["skstmtbkmk"];
		else
			$skstmtbkmk = 0;

		if($row["sksprtbkmk"])
			$sksprtbkmk = $row["sksprtbkmk"];
		else
			$sksprtbkmk = 0;

		if($row["skslptbkmk"])
			$skslptbkmk = $row["skslptbkmk"];
		else
			$skslptbkmk = 0;

		if($row["semestbkmk"])
			$semestbkmk = $row["semestbkmk"];
		else
			$semestbkmk = " ";		

		if($row["kdkeltbkmk"])
			$kdkeltbkmk = $row["kdkeltbkmk"];
		else
			$kdkeltbkmk = "  ";

		if($row["kdkurtbkmk"])
			$kdkurtbkmk = $row["kdkurtbkmk"];
		else
			$kdkurtbkmk = "  ";

		if($row["kdwpltbkmk"])
			$kdwpltbkmk = $row["kdwpltbkmk"];
		else
			$kdwpltbkmk = "  ";

		if($row["nodostbkmk"])
			$nodostbkmk = $row["nodostbkmk"];
		else
			$nodostbkmk = "  ";

		if($row["jenjatbkmk"])
			$jenjatbkmk = $row["jenjatbkmk"];
		else
			$jenjatbkmk = "  ";

		if($row["proditbkmk"])
			$proditbkmk = $row["proditbkmk"];
		else
			$proditbkmk = "  ";

		if($row["stkmktbkmk"])
			$stkmktbkmk = $row["stkmktbkmk"];
		else
			$stkmktbkmk = 0;

		if($row["slbustbkmk"])
			$slbustbkmk = $row["slbustbkmk"];
		else
			$slbustbkmk = "  ";
			
		if($row["sappptbkmk"])
			$sappptbkmk = $row["sappptbkmk"];
		else
			$sappptbkmk = "  ";
			
		if($row["bhnajtbkmk"])
			$bhnajtbkmk = $row["bhnajtbkmk"];
		else
			$bhnajtbkmk = "  ";
		
		if($row["diktttbkmk"])
			$diktttbkmk = $row["diktttbkmk"];
		else
			$diktttbkmk = "  ";

		$JENJATBKMK = "  ";
		$PRODITBKMK = "  ";
		$STKMKTBKMK = "  ";
		$KDUTATBKMK = "  ";
		$KDKUGTBKMK = "  ";
		$KDLAITBKMK = "  ";
		$KDMPATBKMK = "  ";
		$KDMPBTBKMK = "  ";
		$KDMPCTBKMK = "  ";
		$KDMPDTBKMK = "  ";
		$KDMPETBKMK = "  ";
		$KDMPFTBKMK = "  ";
		$KDMPGTBKMK = "  ";
		$KDMPHTBKMK = "  ";
		$KDMPITBKMK = "  ";
		$KDMPJTBKMK = "  ";
		$CRMKLTBKMK = "  ";
		$PRSTDTBKMK = "  ";
		$SMGDSTBKMK = "  ";
		$RPSIMTBKMK = "  ";
		$CSSTUTBKMK = "  ";
		$DISLNTBKMK = "  ";
		$SDILNTBKMK = "  ";
		$CODLNTBKMK = "  ";
		$COLLNTBKMK = "  ";
		$CTXINTBKMK = "  ";
		$PJBLNTBKMK = "  ";
		$PBBLNTBKMK = "  ";
		$UJTLSTBKMK = "  ";
		$TGMKLTBKMK = "  ";
		$TGMODTBKMK = "  ";
		$PSTSITBKMK = "  ";
		$SIMULTBKMK = "  ";
		$LAINNTBKMK = "  ";
		$UJTL1TBKMK = "  ";
		$TGMK1TBKMK = "  ";
		$TGMO1TBKMK = "  ";
		$PSTS1TBKMK = "  ";
		$SIMU1TBKMK = "  ";
		$LAIN1TBKMK = "  ";

	dbase_add_record($db, array(
		$thsmstbkmk,
		$kdptitbkmk,
		$kdjentbkmk,
		$kdpsttbkmk,
		$kdkmktbkmk,
		$nakmktbkmk,
		$sksmktbkmk,
		$skstmtbkmk,
		$sksprtbkmk,
		$skslptbkmk,
		$semestbkmk,
		$kdkeltbkmk,
		$kdkurtbkmk,
		$kdwpltbkmk,
		$nodostbkmk,
		$jenjatbkmk,
		$proditbkmk,
		$stkmktbkmk,
        $slbustbkmk,
		$sappptbkmk,
        $bhnajtbkmk,
		$diktttbkmk,
		
		$KDUTATBKMK,
		$KDKUGTBKMK,
		$KDLAITBKMK,
		$KDMPATBKMK,
		$KDMPBTBKMK,
		$KDMPCTBKMK,
		$KDMPDTBKMK,
		$KDMPETBKMK,
		$KDMPFTBKMK,
		$KDMPGTBKMK,
		$KDMPHTBKMK,
		$KDMPITBKMK,
		$KDMPJTBKMK,
		$CRMKLTBKMK,
		$PRSTDTBKMK,
		$SMGDSTBKMK,
		$RPSIMTBKMK,
		$CSSTUTBKMK,
		$DISLNTBKMK,
		$SDILNTBKMK,
		$CODLNTBKMK,
		$COLLNTBKMK,
		$CTXINTBKMK,
		$PJBLNTBKMK,
		$PBBLNTBKMK,
		$UJTLSTBKMK,
		$TGMKLTBKMK,
		$TGMODTBKMK,
		$PSTSITBKMK,
		$SIMULTBKMK,
		$LAINNTBKMK,
		$UJTL1TBKMK,
		$TGMK1TBKMK,
		$TGMO1TBKMK,
		$PSTS1TBKMK,
		$SIMU1TBKMK,
		$LAIN1TBKMK)); 
	}
}

	if($filter!='all_dbf'){
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_kode;
		$rec['periode'] = $c_periode;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'msmhs';
		$rec['nip'] = Modul::getUserName();
		$rec['namapetugas'] = Modul::getUserDesc();
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		$rec['format'] = $format; 
		$ok = Query::recInsert($conn,$rec,'epsbed.ms_downloadpdpt');
	}
	
	if($filter<>'all_dbf'){		
		$handle = fopen($newfile,"rb");
		echo fread($handle, filesize($newfile));
		fclose($handle);		
		@unlink($newfile);
		ob_end_flush();
	}	

dbase_close($db);
	
?>