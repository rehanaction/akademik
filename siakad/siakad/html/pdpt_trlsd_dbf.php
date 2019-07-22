<?php
	ob_start();

	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = false;
	// pengecekan tipe session user
	$a_auth = Helper::checkRoleAuth($conng,false);

	// otorisasi user
	$c_readlist = $a_auth['canlist'];
	$c_add = $a_auth['cancreate'];
	$c_edit = $a_auth['canedit'];
	$c_delete = $a_auth['candelete'];
	$c_other = $a_auth['canother'];
	
	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	$c_thnkurikulum = $_POST["thnkurikulum"];	
	$c_thnpelaporan = $_POST["tahun"];	
	$c_periode = $_POST["periode"];	
	
	if($filter<>'all_dbf') {
			header("Content-Type: dbf");
			header('Content-Disposition: attachment; filename="TRLSD.DBF"');	
	}
	
	$strSQL = "select * from tran_riwayat_status_dosen where tahun_pelaporan='$c_periode' ";	
	if($c_unit<>'405016')
		$strSQL .= " and kode_program_studi='$c_unit'";
	$rs = $connp->Execute($strSQL);	
		
	$filedbf = Config::fileDbf."TRLSD.DBF";
	$newfile = Config::fileDbfNew."TRLSD.DBF";
	
	copy($filedbf, $newfile);

	$db = dbase_open($newfile,2);

	if ($db) {
	while (!$rs->EOF) 
	{	$i++;
		
		if($rs->fields["tahun_pelaporan"])
			$thnpelaporan = $rs->fields["tahun_pelaporan"];
		else
			$thnpelaporan = "  ";
			
		if($rs->fields["kode_perguruan_tinggi"])
			$kode_perguruan_tinggi = $rs->fields["kode_perguruan_tinggi"];
		else
			$kode_perguruan_tinggi = "  ";

		if($rs->fields["kode_program_studi"])
			$kode_program_studi = $rs->fields["kode_program_studi"];
		else
			$kode_program_studi = "  ";

		if($rs->fields["kode_jenjang_pendidikan"])
			$kode_jenjang_pendidikan = $rs->fields["kode_jenjang_pendidikan"];
		else
			$kode_jenjang_pendidikan = "  ";

		if($rs->fields["nodos_pt"])
			$nodos = $rs->fields["nodos_pt"];
		else
			$nodos = "  ";

		if($rs->fields["status_aktif"])
			$status = $rs->fields["status_aktif"];
		else
			$status = "  ";
			
	dbase_add_record($db, array(
		$thnpelaporan,
		$kode_perguruan_tinggi,
		$kode_program_studi,
		$kode_jenjang_pendidikan,
		$nodos,
		$status
		)); 

	$rs->MoveNext();
	}

}

	if($filter!='all_dbf'){
		$rs_kodeunit = $conn->GetOne("select kodeunit from ms_unit where epskodeprodi='$c_unit'");
		$rec = array();
		$rec['kode_program_studi'] = $rs_kodeunit;
		$rec['kode_program_studi_eps'] = $c_unit;
		$rec['periode'] = $c_periode;
		$rec['thnkurikulum'] = $c_thnkurikulum;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'tran_riwayat_status_dosen';
		$rec['nip'] = $_SESSION['SIAKAD_USER'];
		$rec['namapetugas'] = $_SESSION['SIAKAD_NAME'];
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		$rec['format'] = $format; 
		$ok = $connp->AutoExecute('ms_downloadpdpt',$rec,'INSERT');	
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