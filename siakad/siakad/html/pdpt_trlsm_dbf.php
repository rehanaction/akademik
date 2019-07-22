<?php
	ob_clean();
	
	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	$c_periode = $_POST["tahun"].$_POST['semester'];
	$conn->debug = false;
	
	if($filter<>'all_dbf') {
		header("Content-Type: dbf");
		header('Content-Disposition: attachment; filename="TRLSM.DBF"');
	}	
  
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$strSQL = "select * from epsbed.trlsm where tahunpelaporan='$c_thnpelaporan' and thsmstrlsm='$c_periode' and kdpsttrlsm='$rs_kode' order by nimhstrlsm asc";	
	$rs = $conn->Execute($strSQL);	
	
	$filedbf = "file_dbf/TRLSM.DBF";
	$newfile = "file_dbf/copy/TRLSM.DBF";
	copy($filedbf, $newfile);
	$db = dbase_open($newfile,2);
	
	if ($db) {
		while ($row = $rs->FetchRow()) {
			if($row["thsmstrlsm"])
				$thsmstrlsm = $row["thsmstrlsm"];
			else
				$thsmstrlsm = "  ";

			if($row["kdptitrlsm"])
				$kdptitrlsm = $row["kdptitrlsm"];
			else
				$kdptitrlsm = "  ";

			if($row["kdpsttrlsm"])
				$kdpsttrlsm = $row["kdpsttrlsm"];
			else
				$kdpsttrlsm = "  ";

			if($row["kdjentrlsm"])
				$kdjentrlsm = $row["kdjentrlsm"];
			else
				$kdjentrlsm = "  ";

			if($row["nimhstrlsm"])
				$nimhstrlsm = $row["nimhstrlsm"];
			else
				$nimhstrlsm = "  ";	

			if($row["stmhstrlsm"])
				$stmhstrlsm = $row["stmhstrlsm"];
			else
				$stmhstrlsm = "  ";
			
			if($row["tgllstrlsm"])
				$tgllstrlsm = str_replace("-","",$row["tgllstrlsm"]);
			else
				$tgllstrlsm = date("00010101");
				
			if($row["skstttrlsm"])
				$skstttrlsm = $row["skstttrlsm"];
			else
				$skstttrlsm = "  ";
				
			if($row["nlipktrlsm"])
				$nlipktrlsm = $row["nlipktrlsm"];
			else
				$nlipktrlsm = "  ";
				
			if($row["noskrtrlsm"])
				$noskrtrlsm = $row["noskrtrlsm"];
			else
				$noskrtrlsm = "  ";
				
			if($row["tglretrlsm"])
				$tglretrlsm = str_replace("-","",$row["tglretrlsm"]);
			else
				$tglretrlsm = date("00010101");
			
			if($row["noijjatrlsm"])
				$noijjatrlsm = $row["noijjatrlsm"];
			else
				$noijjatrlsm = "  ";
				
			if($row["stllstrlsm"])
				$stllstrlsm = $row["stllstrlsm"];
			else
				$stllstrlsm = "  ";
				
			if($row["jnllstrlsm"])
				$jnllstrlsm = $row["jnllstrlsm"];
			else
				$jnllstrlsm = "  ";
			
			if($row["blawltrlsm"])
				$blawltrlsm = $row["blawltrlsm"];
			else
				$blawltrlsm = "  ";
				
			if($row["blakhtrlsm"])
				$blakhtrlsm = $row["blakhtrlsm"];
			else
				$blakhtrlsm = "  ";
			
			if($row["nods1trlsm"])
				$nods1trlsm = $row["nods1trlsm"];
			else
				$nods1trlsm = "  ";
			
			if($row["nods2trlsm"])
				$nods2trlsm = $row["nods2trlsm"];
			else
				$nods2trlsm = "  ";
				
			if($row["nods3trlsm"])
				$nods3trlsm = $row["nods3trlsm"];
			else
				$nods3trlsm = "  ";
				
			if($row["nods4trlsm"])
				$nods4trlsm = $row["nods4trlsm"];
			else
				$nods4trlsm = "  ";
			
			if($row["nods5trlsm"])
				$nods5trlsm = $row["nods5trlsm"];
			else
				$nods5trlsm = "  ";
				
		dbase_add_record($db, array(
			$thsmstrlsm,
			$kdptitrlsm,
			$kdjentrlsm,
			$kdpsttrlsm,
			$nimhstrlsm,
			$stmhstrlsm,
			$tgllstrlsm,
			$skstttrlsm,
			$nlipktrlsm,
			$noskrtrlsm,
			$tglretrlsm,
			$noijjatrlsm,
			$stllstrlsm,
			$jnllstrlsm,
			$blawltrlsm,
			$blakhtrlsm,
			$nods1trlsm,
			$nods2trlsm,
			$nods3trlsm,
			$nods4trlsm,
			$nods5trlsm)); 
		}
	}
	
	if($filter!='all'){
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_kode;
		$rec['periode'] = $c_periode;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'trlsm';
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