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
			header('Content-Disposition: attachment; filename="MSMHS.DBF"');	
	}
	
	$strSQL = "select * from epsbed.msmhs where tahunpelaporan='$c_thnpelaporan' and kdpstmsmhs='$rs_kode' order by nimhsmsmhs asc";	
	$rs = $conn->Execute($strSQL);
	
	$filedbf = "file_dbf/MSMHS.DBF";
	$newfile = "file_dbf/copy/MSMHS.DBF";
	
	$cp = copy($filedbf, $newfile);
	$db = dbase_open($newfile,2);
	if ($db) {
		while ($row = $rs->FetchRow()) {
			//echo $row["nimhsmsmhs"]."<br>";
			if($row["thsmstbkmk"])
				$thsmstbkmk = $row["thsmstbkmk"];
			else
				$thsmstbkmk = "  ";
				
			if($row["kdptimsmhs"])
				$kdptimsmhs = $row["kdptimsmhs"];
			else
				$kdptimsmhs = "  ";

			if($row["kdpstmsmhs"])
				$kdpstmsmhs = $row["kdpstmsmhs"];
			else
				$kdpstmsmhs = "  ";
				
			if($row["kdjenmsmhs"])
				$kdjenmsmhs = $row["kdjenmsmhs"];
			else
				$kdjenmsmhs = "  ";

			if($row["nimhsmsmhs"])
				$nimhsmsmhs = $row["nimhsmsmhs"];
			else
				$nimhsmsmhs = "  ";
			
			if($row["nmmhsmsmhs"])
				$nmmhsmsmhs = $row["nmmhsmsmhs"];
			else
				$nmmhsmsmhs = "  ";
				
			if($row["shiftmsmhs"])
				$shiftmsmhs = $row["shiftmsmhs"];
			else
				$shiftmsmhs = "R";
			//$shiftmsmhs='R';
			
			if($row["tplhrmsmhs"])
				$tplhrmsmhs = $row["tplhrmsmhs"];
			else
				$tplhrmsmhs = "  ";

			if($row["tglhrmsmhs"]){
				//$arrtgl=explode("-",$row["tglhrmsmhs"]);
				//$tglhrmsmhs = $arrtgl[1].$arrtgl[2].$arrtgl[0];
				$tglhrmsmhs = str_replace("-","",$row['tglhrmsmhs']);
			}else{
				$tglhrmsmhs = date("01010001");
			}
			if($row["kdjekmsmhs"])
				$kdjekmsmhs = $row["kdjekmsmhs"];
			else
				$kdjekmsmhs = "  ";

			if($row["tahunmsmhs"])
				$tahunmsmhs = $row["tahunmsmhs"];
			else
				$tahunmsmhs = "  ";

			if($row["smawlmsmhs"])
				$smawlmsmhs = $row["smawlmsmhs"];
			else
				$smawlmsmhs = "  ";
			
			if($row["btstumsmhs"])
				$btstumsmhs = $row["btstumsmhs"];
			else
				$btstumsmhs = "  ";
				
			if($row["assmamsmhs"])
				$assmamsmhs = $row["assmamsmhs"];
			else
				$assmamsmhs = "  ";
				
			if($row["tgmskmsmhs"])
				$tgmskmsmhs = str_replace("-","",$row["tgmskmsmhs"]);
			else
				$tgmskmsmhs = date("00010101");		

			if($row["tgllsmmsmhs"])
				$tgllsmmsmhs = date('m/d/Y',strtotime($row["tgllsmmsmhs"]));
			else
				$tgllsmmsmhs = date("00010101");

			if($row["stmhsmsmhs"])
				$stmhsmsmhs = $row["stmhsmsmhs"];
			else
				$stmhsmsmhs = "  ";

			if($row["stpidmsmhs"])
				$stpidmsmhs = $row["stpidmsmhs"];
			else
				$stpidmsmhs = "  ";

			if($row["sksdimsmhs"])
				$sksdimsmhs = $row["sksdimsmhs"];
			else
				$sksdimsmhs = 0;

			if($row["asnimmsmhs"])
				$asnimmsmhs = $row["asnimmsmhs"];
			else
				$asnimmsmhs = "  ";

			if($row["asptimsmhs"])
				$asptimsmhs = $row["asptimsmhs"];
			else
				$asptimsmhs = "  ";

			if($row["asjenmsmhs"])
				$asjenmsmhs = $row["asjenmsmhs"];
			else
				$asjenmsmhs = 0;

			if($row["aspstmsmhs"])
				$aspstmsmhs = $row["aspstmsmhs"];
			else
				$aspstmsmhs = "  ";

			if($row["bistumsmhs"])
				$bistumsmhs = $row["bistumsmhs"];
			else
				$bistumsmhs = "  ";

			if($row["peksbmsmhs"])
				$peksbmsmhs = $row["peksbmsmhs"];
			else
				$peksbmsmhs = "  ";

			if($row["nmpekmsmhs"])
				$nmpekmsmhs = $row["nmpekmsmhs"];
			else
				$nmpekmsmhs = "  ";

			if($row["ptpekmsmhs"])
				$ptpekmsmhs = $row["ptpekmsmhs"];
			else
				$ptpekmsmhs = "  ";

			if($row["pspekmsmhs"])
				$pspekmsmhs = $row["pspekmsmhs"];
			else
				$pspekmsmhs = "  ";

			if($row["nmprmmsmhs"])
				$nmprmmsmhs = $row["nmprmmsmhs"];
			else
				$nmprmmsmhs = "  ";
			
			if($row["nokp1msmhs"])
				$nokp1msmhs = $row["nokp1msmhs"];
			else
				$nokp1msmhs = "  ";

			if($row["nokp2msmhs"])
				$nokp2msmhs = $row["nokp2msmhs"];
			else
				$nokp2msmhs = "  ";

			if($row["nokp3msmhs"])
				$nokp3msmhs = $row["nokp3msmhs"];
			else
				$nokp3msmhs = "  ";

			if($row["nokp4msmhs"])
				$nokp4msmhs = $row["nokp4msmhs"];
			else
				$nokp4msmhs = "  ";

			dbase_add_record($db, array(
				$kdptimsmhs, 
				$kdjenmsmhs, 
				$kdpstmsmhs, 
				$nimhsmsmhs,	
				$nmmhsmsmhs,
				$shiftmsmhs,
				$tplhrmsmhs, 
				$tglhrmsmhs, 
				$kdjekmsmhs, 
				$tahunmsmhs,	
				$smawlmsmhs,
				$btstumsmhs, 
				$assmamsmhs, 
				$tgmskmsmhs, 
				$tgllsmmsmhs, 
				$stmhsmsmhs,
				$stpidmsmhs, 
				$sksdimsmhs, 
				$asnimmsmhs, 
				$asptimsmhs, 
				$asjenmsmhs,
				$aspstmsmhs,
				$bistumsmhs, 
				$peksbmsmhs, 
				$nmpekmsmhs, 
				$ptpekmsmhs, 
				$pspekmsmhs,
				$nmprmmsmhs, 
				$nokp1msmhs, 
				$nokp2msmhs, 
				$nokp3msmhs, 
				$nokp4msmhs)); 
		}
	}else{
		echo 'gagal open dbase';
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
	//die('hentikan');
	if($filter<>'all_dbf'){		
		$handle = fopen($newfile,"rb");
		echo fread($handle, filesize($newfile));
		fclose($handle);		
		@unlink($newfile);
		ob_end_flush();
	}	

dbase_close($db);
?>
