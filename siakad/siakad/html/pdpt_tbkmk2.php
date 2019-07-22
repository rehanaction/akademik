<?php
	ob_clean();

	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	//$conn->debug = true;
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	$c_periode = $_POST["tahun"].$_POST['semester'];	
	
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	if($filter<>'all') {
		header("Content-Type: text/csv");
		header('Content-Disposition: attachment; filename="TBKMK.csv"');
	}
		
	$strSQL = "select * from epsbed.tbkmk where tahunpelaporan='$c_thnpelaporan' and thsmstbkmk='$c_periode' and kdpsttbkmk='$rs_kode' order by idtbkmk asc";		
	$rs = $conn->Execute($strSQL);	
	
	echo 'THSMSTBKMK;KDPTITBKMK;KDJENTBKMK;KDPSTTBKMK;KDKMKTBKMK;NAKMKTBKMK;SKSMKTBKMK;'.
			'SKSTMTBKMK;SKSPRTBKMK;SKSLPTBKMK;SEMESTBKMK;KDWPLTBKMK;KDKURTBKMK;KDKELTBKMK;'.
			'NODOSTBKMK;JENJATBKMK;PRODITBKMK;STKMKTBKMK;SLBUSTBKMK;SAPPPTBKMK;BHNAJTBKMK;'.
			'DIKTTTBKMK'."\n";
	while ($row = $rs->FetchRow()) {
		echo $row["thsmstbkmk"].';'.$row["kdptitbkmk"].';'.
			 $row["kdjentbkmk"].';'.$row["kdpsttbkmk"].';'.
			 $row["kdkmktbkmk"].';'.$row["nakmktbkmk"].';'.
			 $row["sksmktbkmk"].';'.$row["skstmtbkmk"].';'.
			 $row["sksprtbkmk"].';'.$row["skslptbkmk"].';'.
			 $row["semestbkmk"].';'.$row["kdwpltbkmk"].';'.
			 $row["kdkurtbkmk"].';'.$row["kdkeltbkmk"].';'.
			 $row["nodostbkmk"].';'.$row["jenjatbkmk"].';'.		
			 $row["proditbkmk"].';'.$row["stkmktbkmk"].';'.		
			 $row["slbustbkmk"].';'.$row["sappptbkmk"].';'.
			 $row["bhnajtbkmk"].';'.$row["diktttbkmk"]."\n";
	}
	
	if($filter!='all'){
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_kode;
		$rec['periode'] = $c_periode;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'trakd';
		$rec['nip'] = Modul::getUserName();
		$rec['namapetugas'] = Modul::getUserDesc();
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		$rec['format'] = $format; 
		$ok = Query::recInsert($conn,$rec,'epsbed.ms_downloadpdpt');
	}
	
	if($filter=='all') {
		$file = 'pdpt/TBKMK.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>
