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
		header('Content-Disposition: attachment; filename="TRNLP.DBF"');	
	}
	
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$strSQL = "select * from epsbed.trnlp where tahunpelaporan='$c_thnpelaporan' and thsmstrnlp='$c_periode' and kdpsttrnlp='$rs_kode' order by nimhstrnlp asc";	
	$rs = $conn->Execute($strSQL);	
			
	$filedbf = "file_dbf/TRNLP.DBF";
	$newfile = "file_dbf/copy/TRNLP.DBF";
	copy($filedbf, $newfile);
	$db = dbase_open($newfile,2);
	
	if ($db) {
		while ($row = $rs->FetchRow()) {
			
			if($row["thsmstrnlp"])
				$thsmstrnlp = $row["thsmstrnlp"];
			else
				$thsmstrnlp = "  ";

			if($row["kdptitrnlp"])
				$kdptitrnlp = $row["kdptitrnlp"];
			else
				$kdptitrnlp = "  ";

			if($row["kdpsttrnlp"])
				$kdpsttrnlp = $row["kdpsttrnlp"];
			else
				$kdpsttrnlp = "  ";

			if($row["kdjentrnlp"])
				$kdjentrnlp = $row["kdjentrnlp"];
			else
				$kdjentrnlp = "  ";

			if($row["nimhstrnlp"])
				$nimhstrnlp = $row["nimhstrnlp"];
			else
				$nimhstrnlp = "  ";	

			if($row["kdkmktrnlp"])
				$kdkmktrnlp = $row["kdkmktrnlp"];
			else
				$kdkmktrnlp = "  ";

			if($row["nlakhtrnlp"])
				$nlakhtrnlp = $row["nlakhtrnlp"];
			else
				$nlakhtrnlp = "  ";

			if($row["bobottrnlp"])
				$bobottrnlp = $row["bobottrnlp"];
			else
				$bobottrnlp = 0;

			if($row["kelastrnlp"])
				$kelastrnlp  = $row["kelastrnlp"];
			else
				$kelastrnlp  = "  ";

		dbase_add_record($db, array(
			$thsmstrnlp,
			$kdptitrnlp,
			$kdjentrnlp,
			$kdpsttrnlp,
			$nimhstrnlp,
			$kdkmktrnlp,
			$nlakhtrnlp,
			$bobottrnlp,
			$kelastrnlp)); 
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
