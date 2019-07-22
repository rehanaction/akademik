<?php
	ob_clean();
	
	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	$c_periode = $_POST["tahun"].$_POST['semester'];
	
	if($filter<>'all') {
		header("Content-Type: text/csv");
		header('Content-Disposition: attachment; filename="TRAKD_ADM.csv"');	
	}
  
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$strSQL = "select * from epsbed.trakd where tahunpelaporan='$c_thnpelaporan' and thsmstrakd='$c_periode' and kdpsttrakd='$rs_kode' order by idtrakd asc";	
	$rs = $conn->Execute($strSQL);	
	
	echo 'THSMSTRAKD;KDPTITRAKD;KDPSTTRAKD;KDJENTRAKD;NODOSTRAKD;KDKMKTRKAD;KELASTRAKD;TMRENTRAKD;TMRELTRAKD;NAMAMK;SEMESTERTRAKD;NAMADOSEN'."\n";
	
	while ($row = $rs->FetchRow()) {
		echo $row["thsmstrakd"].';'.$row["kdptitrakd"].';'.
			 $row["kdpsttrakd"].';'.$row["kdjentrakd"].';'.
			 $row["nodostrakd"].';'.$row["kdkmktrakd"].';'.
			 $row["kelastrakd"].';'.$row["tmrentrakd"].';'.
			 $row["tmreltrakd"].';'.$row["namamk"].';'.
			 $row["semestertrakd"].';'.$row["namadosen"]."\n"; 
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
	
	if($filter=='all') {
		$file = 'pdpt/TRAKD_ADM.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>
