<?php
	ob_clean();
	
	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	$c_periode = $_POST["tahun"].$_POST['semester'];	
	
	if($filter<>'all') {
		header("Content-Type: text/csv");
		header('Content-Disposition: attachment; filename="TRNLP.csv"');	
	}
  
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$strSQL = "select * from epsbed.trnlp where tahunpelaporan='$c_thnpelaporan' and thsmstrnlp='$c_periode' and kdpsttrnlp='$rs_kode' order by nimhstrnlp asc";	
	$rs = $conn->Execute($strSQL);	
		  
	echo 'THSMSTRNLP;KDPTITRNLP;KDJENTRNLP;KDPSTTRNLP;NIMHSTRNLP;KDKMKTRNLP;'.
			'NLAKHTRNLP;BOBOTTRNLP;KELASTRNLP'."\n";
	
	while ($row = $rs->FetchRow()) {
		echo $row["thsmstrnlp"].';'.$row["kdptitrnlp"].';'.
			 $row["kdjentrnlp"].';'.$row["kdpsttrnlp"].';'.
			 $row["nimhstrnlp"].';'.$row["kdkmktrnlp"].';'.
			 $row["nlakhtrnlp"].';'.$row["bobottrnlp"].';'.
			 $row["kelastrnlp"]."\n";
	}
	
	if($filter!='all'){
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_kode;
		$rec['periode'] = $c_periode;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'trnlp';
		$rec['nip'] = Modul::getUserName();
		$rec['namapetugas'] = Modul::getUserDesc();
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		$rec['format'] = $format; 
		$ok = Query::recInsert($conn,$rec,'epsbed.ms_downloadpdpt');
	}
	
	
	if($filter=='all') {
		$file = 'pdpt/TRNLP.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>
