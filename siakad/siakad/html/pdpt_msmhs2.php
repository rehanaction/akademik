<?php
	ob_clean();

	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	$c_periode = $_POST["tahun"].$_POST['semester'];	
	//$conn->debug=true;
	//ambil kodeepsbedprodi
	$conn->debug = false;
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	if($filter<>'all') {
		header("Content-Type: text/csv");
		header('Content-Disposition: attachment; filename="MSMHS.csv"');	
	}
  
	$tableTitle = ".: Data Mahasiswa :.";
	$columnCount = 33;
	
	$strSQL = "select * from epsbed.msmhs where tahunpelaporan='$c_thnpelaporan' and kdpstmsmhs='$rs_kode' order by nimhsmsmhs asc";	
	$rs = $conn->Execute($strSQL);	
	
	echo 'KDPTIMSMHS;KDJENMSMHS;KDPSTMSMHS;NIMHSMSMHS;NMMHSMSMHS;SHIFTMSMHS;'.
		  'TPLHRMSMHS;TGLHRMSMHS;KDJEKMSMHS;TAHUNMSMHS;'.
		  'SMAWLMSMHS;BTSTUMSMHS;ASSMAMSMHS;TGMSKMSMHS;'.
		  'TGLLSMSMHS;STMHSMSMHS;STPIDMSMHS;SKSDIMSMHS;'.
		  'ASNIMMSMHS;ASPTIMSMHS;ASJENMSMHS;ASPSTMSMHS;'.
		  'BISTUMSMHS;PEKSBMSMHS;NMPEKMSMHS;PTPEKMSMHS;PSPEKMSMHS;'.
		  'NMPRMMSMHS;NOKP1MSMHS;NOKP2MSMHS;NOKP3MSMHS;'.
		  'NOKP4MSMHS'."\n";
	while ($row = $rs->FetchRow()) {
		$tgllahir=!empty($row["tglhrmsmhs"])?date('d/m/Y',strtotime($row["tglhrmsmhs"])):'01/01/0001';
		$tgllsmmsmhs=!empty($row["tgllsmmsmhs"])?date('d/m/Y',strtotime($row["tgllsmmsmhs"])):'01/01/0001';
		$shiftmsmhs=!empty($row["shiftmsmhs"])?$row["shiftmsmhs"]:'R';
		//echo $row["nimhsmsmhs"]."<br>";	
		echo $row["kdptimsmhs"].';'.$row["kdjenmsmhs"].';'.$row["kdpstmsmhs"].';'.
			 $row["nimhsmsmhs"].';'.$row["nmmhsmsmhs"].';'.$shiftmsmhs.';'.$row["tplhrmsmhs"].';'.			
			$tgllahir.';'.$row["kdjekmsmhs"].';'.$row["tahunmsmhs"].';'.
			 $row["smawlmsmhs"].';'.$row["btstumsmhs"].';'.$row["assmamsmhs"].';'.$row["tgmskmsmhs"].';'.
			 $tgllsmmsmhs.';'.$row["stmhsmsmhs"].';'.$row["stpidmsmhs"].';'.
			 $row["sksdimsmhs"].';'.$row["asnimmsmhs"].';'.$row["asptimsmhs"].';'.
			 $row["asjenmsmhs"].';'.$row["aspstmsmhs"].';'.$row["bistumsmhs"].';'.
			 $row["peksbmsmhs"].';'.$row["nmpekmsmhs"].';'.$row["ptpekmsmhs"].';'.$row["pspekmsmhs"].';'.
			 $row["nmprmmsmhs"].';'.$row["nokp1msmhs"].';'.$row["nokp2msmhs"].';'.
			 $row["nokp3msmhs"].';'.$row["nokp4msmhs"].';'."\n";
	}
//die('berakhir');
	if($filter!='all'){
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
	
	if($filter=='all') {
		$file = 'pdpt/MSMHS.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>
