<?php
	ob_clean();
	
	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	$c_periode = $_POST["tahun"].$_POST['semester'];	
	
	if($filter<>'all') {
		header("Content-Type: text/csv");
		header('Content-Disposition: attachment; filename="TRLSM.csv"');
	}	
	
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$strSQL = "select * from epsbed.trlsm where tahunpelaporan='$c_thnpelaporan' and thsmstrlsm='$c_periode' and kdpsttrlsm='$rs_kode' order by nimhstrlsm asc";	
	$rs = $conn->Execute($strSQL);	
	
	echo 'THSMSTRLSM;KDPTITRLSM;KDJENTRLSM;KDPSTTRLSM;NIMHSTRLSM;STMHSTRLSM;'.
			'TGLLSTRLSM;SKSTTTRLSM;NLIPKTRLSM;NOSKRTRLSM;TGLRETRLSM;NOIJATRLSM;'.
			'STLLSTRLSM;JNLLSTRLSM;BLAWLTRLSM;BLAKHTRLSM;NODS1TRLSM;NODS2TRLSM;'.
			'NODS3TRLSM;NODS4TRLSM;NODS5TRLSM'."\n";
	
	while ($row = $rs->FetchRow()) {
		echo $row["thsmstrlsm"].';'.$row["kdptitrlsm"].';'.
			 $row["kdjentrlsm"].';'.$row["kdpsttrlsm"].';'.
			 $row["nimhstrlsm"].';'.$row["stmhstrlsm"].';'.
			 $row["tgllstrlsm"].';'.$row["skstttrlsm"].';'.
			 $row["nlipktrlsm"].';'.$row["noskrtrlsm"].';'.
			 $row["tglretrlsm"].';'.$row["noijjatrlsm"].';'.
			 $row["stllstrlsm"].';'.$row["jnllstrlsm"].';'.
			 $row["blawltrlsm"].';'.$row["blakhtrlsm"].';'.
			 $row["nods1trlsm"].';'.$row["nods2trlsm"].';'.
			 $row["nods3trlsm"].';'.$row["nods4trlsm"].';'.
			 $row["nods5trlsm"]."\n";
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
	
	if($filter=='all') {
		$file = 'pdpt/TRLSM.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>
