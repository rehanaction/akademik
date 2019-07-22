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
		header('Content-Disposition: attachment; filename="TRAKD_ADM.DBF"');	
	}
  
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$strSQL = "select * from epsbed.trakd where tahunpelaporan='$c_thnpelaporan' and thsmstrakd='$c_periode' and kdpsttrakd='$rs_kode' order by idtrakd asc";	
	$rs = $conn->Execute($strSQL);	
	
	$filedbf = "file_dbf/TRAKD_ADM.DBF";
	$newfile = "file_dbf/copy/TRAKD_ADM.DBF";
	copy($filedbf, $newfile);
	$db = dbase_open($newfile,2);
	
	if ($db) {
		while ($row = $rs->FetchRow()) {
			
			if($row["thsmstrakd"])
				$thsmstrakd = $row["thsmstrakd"];
			else
				$thsmstrakd = "  ";

			if($row["kdptitrakd"])
				$kdptitrakd = $row["kdptitrakd"];
			else
				$kdptitrakd = "  ";

			if($row["kdpsttrakd"])
				$kdpsttrakd = $row["kdpsttrakd"];
			else
				$kdpsttrakd = "  ";

			if($row["kdjentrakd"])
				$kdjentrakd = $row["kdjentrakd"];
			else
				$kdjentrakd = "  ";

			if($row["nodostrakd"])
				$nodostrakd = $row["nodostrakd"];
			else
				$nodostrakd = "  ";	

			if($row["kdkmktrakd"])
				$kdkmktrakd = $row["kdkmktrakd"];
			else
				$kdkmktrakd = "  ";

			if($row["kelastrakd"])
				$kelastrakd = $row["kelastrakd"];
			else
				$kelastrakd = "  ";

			if($row["tmrentrakd"])
				$tmrentrakd = $row["tmrentrakd"];
			else
				$tmrentrakd = 0;

			if($row["tmreltrakd"])
				$tmreltrakd = $row["tmreltrakd"];
			else
				$tmreltrakd = 0;
				
			if($row["namamk"])
				$namamk = $row["namamk"];
			else
				$namamk = "  ";
				
			if($row["namadosen"])
				$namadosen = $row["namadosen"];
			else
				$namadosen = "  ";
			if($row["semestertrakd"])
				$semmk = $row["semestertrakd"];
			else
				$semmk = "  ";
		dbase_add_record($db, array(
			$thsmstrakd,
			$kdptitrakd,
			$kdjentrakd,
			$kdpsttrakd,
			$nodostrakd,
			$kdkmktrakd,
			$kelastrakd,
			$tmrentrakd,
			$tmreltrakd,
			$namamk,
			$semmk,
			$namadosen)); 
		}
	}

	if($filter!='all'){
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_kode;
		$rec['periode'] = $c_periode;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'trakd_adm';
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
