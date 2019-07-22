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
		header('Content-Disposition: attachment; filename="TRNLM.DBF"');	
	}
	
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$strSQL = "select * from epsbed.trnlm where tahunpelaporan='$c_thnpelaporan' and thsmstrnlm='$c_periode' and kdpsttrnlm='$rs_kode' order by idtrnlm asc";	
	$rs = $conn->Execute($strSQL);	
			
	$filedbf = "file_dbf/TRNLM.DBF";
	$newfile = "file_dbf/copy/TRNLM.DBF";
	copy($filedbf, $newfile);
	$db = dbase_open($newfile,2);
	
	if ($db) {
		while ($row = $rs->FetchRow()) {
			
			if($row["thsmstrnlm"])
				$thsmstrnlm = $row["thsmstrnlm"];
			else
				$thsmstrnlm = "  ";

			if($row["kdptitrnlm"])
				$kdptitrnlm = $row["kdptitrnlm"];
			else
				$kdptitrnlm = "  ";

			if($row["kdpsttrnlm"])
				$kdpsttrnlm = $row["kdpsttrnlm"];
			else
				$kdpsttrnlm = "  ";

			if($row["kdjentrnlm"])
				$kdjentrnlm = $row["kdjentrnlm"];
			else
				$kdjentrnlm = "  ";

			if($row["nimhstrnlm"])
				$nimhstrnlm = $row["nimhstrnlm"];
			else
				$nimhstrnlm = "  ";	

			if($row["kdkmktrnlm"])
				$kdkmktrnlm = $row["kdkmktrnlm"];
			else
				$kdkmktrnlm = "  ";

			if($row["nlakhtrnlm"])
				$nlakhtrnlm = $row["nlakhtrnlm"];
			else
				$nlakhtrnlm = "  ";

			if($row["bobottrnlm"])
				$bobottrnlm = $row["bobottrnlm"];
			else
				$bobottrnlm = 0;

			if($row["kelastrnlm"])
				$kelastrnlm  = $row["kelastrnlm"];
			else
				$kelastrnlm  = "  ";

		dbase_add_record($db, array(
			$thsmstrnlm,
			$kdptitrnlm,
			$kdjentrnlm,
			$kdpsttrnlm,
			$nimhstrnlm,
			$kdkmktrnlm,
			$nlakhtrnlm,
			$bobottrnlm,
			$kelastrnlm)); 
		}	
	}
	
	if($filter!='all'){
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_kode;
		$rec['periode'] = $c_periode;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'trnlm';
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