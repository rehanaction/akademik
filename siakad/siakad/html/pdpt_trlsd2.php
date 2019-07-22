<?php
	ob_start();

	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// pengecekan tipe session user
	$a_auth = Helper::checkRoleAuth($conng,false);

	// otorisasi user
	$c_readlist = $a_auth['canlist'];
	$c_add = $a_auth['cancreate'];
	$c_edit = $a_auth['canedit'];
	$c_delete = $a_auth['candelete'];
	$c_other = $a_auth['canother'];

	//include("function.inc.php");
	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	$c_thnkurikulum = $_POST["thnkurikulum"];	
	$c_thnpelaporan = $_POST["tahun"];	
	$c_periode = $_POST["periode"];	

	if($filter<>'all') {
			header("Content-Type: text/csv");
			header('Content-Disposition: attachment; filename="TRLSD.csv"');	
	}
  
	$tableTitle = ".: Data Transaksi Dosen Cuti/Meninggal/Studi Lanjut :.";
	$columnCount = 33;
	// $rs_kodeprodi_eps = $conn->GetOne("select epskodeprodi from ms_unit where kodeunit='$c_unit'");
	
	$strSQL = "select * from tran_riwayat_status_dosen where tahun_pelaporan='$c_periode' ";	
	if($c_unit<>'405016')
		$strSQL .= " and kode_program_studi='$c_unit'";
	
	$rs = $connp->Execute($strSQL);	
	echo 'THSMSTRLSD,KDPTITRLSD,KDPSTTRLSD,KDJENTRLSD,NODOSTRLSD,STDOSTRLSD'."\n";		  
	while (!$rs->EOF) 
	{	$i++;
		echo '"'.$rs->fields["tahun_pelaporan"].'","'.$rs->fields["kode_perguruan_tinggi"].'",'.
			 '"'.$rs->fields["kode_program_studi"].'","'.$rs->fields["kode_jenjang_pendidikan"].'",'.
			 '"'.$rs->fields["nodos_pt"].'",'.
			 '"'.$rs->fields["status_aktif"].'"'."\n";
		$rs->MoveNext();
	}
	if($filter!='all'){
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
	
	if($filter=='all') {
		$file = 'pdpt/TRLSD.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>