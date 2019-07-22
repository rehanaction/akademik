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
		header('Content-Disposition: attachment; filename="MSDOS.csv"');	
	}
  
	$tableTitle = ".: Data Dosen :.";
	$columnCount = 33;
	// $rs_kodeprodi_eps = $conn->GetOne("select epskodeprodi from ms_unit where kodeunit='$c_unit'");
	$strSQL = "select * from tmst_dosen where 1=1";	
	if($c_unit<>'405016')
		$strSQL .= " and kode_program_studi='$c_unit'";
	$rs = $connp->Execute($strSQL);	
	
	echo 'KDPTIMSDOS,KDJENMSDOS,NOKTPMSDOS,TPLHRMSDOS,NODOSMSDOS,NMDOSMSDOS,GELARMSDOS,TGLHRMSDOS,'.
		  'KDJEKMSDOS,KDJANMSDOS,KDPDAMSDOS,KDSTAMSDOS,NIPNSMSDOS,STDOSMSDOS,MLSEMMSDOS,PTINDMSDOS,NIDNNMSDOS'."\n";
		  
	while (!$rs->EOF) 
	{	$i++;
		echo '"'.$rs->fields["kode_perguruan_tinggi"].'","'.$rs->fields["kode_jenjang_pendidikan"].'",'.
			 '"'.$rs->fields["nomor_ktp"].'","'.$rs->fields["tempat_lahir"].'",'.
			 '"'.$rs->fields["nidn"].'","'.$rs->fields["nama_dosen"].'",'.
			 '"'.$rs->fields["gelar_akademik"].'","'.$rs->fields["tanggal_lahir"].'",'.
			 '"'.$rs->fields["jenis_kelamin"].'","'.$rs->fields["kode_jabatan_akademik"].'",'.
			 '"'.$rs->fields["kode_pendidikan_tertinggi"].'","'.$rs->fields["ikatan_kerja"].'",'.
			 '"'.$rs->fields["nip_pns"].'","'.$rs->fields["status_aktif"].'",'.
			 '"'.$rs->fields["mulai_semester"].'","'.$rs->fields["kode_perguruan_tinggi"].'",'.
			 '"'.$rs->fields["nidn"].'"'."\n";
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
		$rec['tabelpdpt'] = 'tmst_dosen';
		$rec['nip'] = $_SESSION['SIAKAD_USER'];
		$rec['namapetugas'] = $_SESSION['SIAKAD_NAME'];
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		$rec['format'] = $format; 
		$ok = $connp->AutoExecute('ms_downloadpdpt',$rec,'INSERT');	
	}
	
	if($filter=='all') {
		$file = 'pdpt/MSDOS.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>