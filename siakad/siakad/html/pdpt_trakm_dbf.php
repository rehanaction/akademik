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
			header('Content-Disposition: attachment; filename="TRAKM.DBF"');
	}
		 
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$strSQL = "select * from epsbed.trakm where tahunpelaporan='$c_thnpelaporan' and kdpsttrakm='$rs_kode' order by nimhstrakm asc";	
	$rs = $conn->Execute($strSQL);	
	
	$filedbf = "file_dbf/TRAKM.DBF";
	$newfile = "file_dbf/copy/TRAKM.DBF";
	copy($filedbf, $newfile);
	$db = dbase_open($newfile,2);
	
	if ($db) {
		while ($row = $rs->FetchRow()) {
			if($row["thsmstrakm"])
				$thsmstrakm = $row["thsmstrakm"];
			else
				$thsmstrakm = "  ";

			if($row["kdptitrakm"])
				$kdptitrakm = $row["kdptitrakm"];
			else
				$kdptitrakm = "  ";

			if($row["kdpsttrakm"])
				$kdpsttrakm = $row["kdpsttrakm"];
			else
				$kdpsttrakm = "  ";

			if($row["kdjentrakm"])
				$kdjentrakm = $row["kdjentrakm"];
			else
				$kdjentrakm = "  ";

			if($row["nimhstrakm"])
				$nimhstrakm = $row["nimhstrakm"];
			else
				$nimhstrakm = "  ";	

			if($row["nlipstrakm"])
				$nlipstrakm = $row["nlipstrakm"];
			else
				$nlipstrakm = 0;

			if($row["sksemtrakm"])
				$sksemtrakm = $row["sksemtrakm"];
			else
				$sksemtrakm = 0;

			if($row["nlipktrakm"])
				$nlipktrakm = $row["nlipktrakm"];
			else
				$nlipktrakm = 0;

			if($row["skstttrakm"])
				$skstttrakm = $row["skstttrakm"];
			else
				$skstttrakm = 0;

		dbase_add_record($db, array(
			$thsmstrakm,
			$kdptitrakm,
			$kdjentrakm,
			$kdpsttrakm,
			$nimhstrakm,
			$sksemtrakm,
			$nlipstrakm,
			$skstttrakm,
			$nlipktrakm)); 
		}
	}

	if($filter!='all_dbf'){
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_kode;
		$rec['periode'] = $c_periode;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'trakm';
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