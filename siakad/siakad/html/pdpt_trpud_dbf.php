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
			header('Content-Disposition: attachment; filename="TRPUD.DBF"');	
	}
	
	$strSQL = "select * from tran_publikasi_dosen_tetap where tahun_pelaporan like '".$c_thnpelaporan."%' ";	
	if($c_unit<>'405016')
		$strSQL .= " and kode_program_studi='$c_unit'";
	$rs = $connp->Execute($strSQL);	
		
	$filedbf = Config::fileDbf."TRPUD.DBF";
	$newfile = Config::fileDbfNew."TRPUD.DBF";
	
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
	
		if($rs->fields["kode_jenjang_pendidikan"])
			$kode_jenjang_pendidikan = $rs->fields["kode_jenjang_pendidikan"];
		else
			$kode_jenjang_pendidikan = "  ";

		if($rs->fields["kode_program_studi"])
			$kode_program_studi = $rs->fields["kode_program_studi"];
		else
			$kode_program_studi = "  ";

		if($rs->fields["nidn"])
			$nidn = $rs->fields["nidn"];
		else
			$nidn = "  ";

		if($rs->fields["no_penelitian"])
			$nourut = $rs->fields["no_penelitian"];
		else
			$nourut = "  ";

		if($rs->fields["jenis_penelitian"])
			$jenispenelitian = $rs->fields["jenis_penelitian"];
		else
			$jenispenelitian = "  ";

		if($rs->fields["media_publikasi"])
			$media = $rs->fields["media_publikasi"];
		else
			$media = "  ";	
		
		if($rs->fields["kode_pengarang"])
			$pengarang = $rs->fields["kode_pengarang"];
		else
			$pengarang = "  ";	
			
		if($rs->fields["mandiritim"])
			$mandiritim = $rs->fields["mandiritim"];
		else
			$mandiritim = "  ";

		if($rs->fields["tahunbulanpublikasi"])
			$tahunbulanpublikasi = $rs->fields["tahunbulanpublikasi"];
		else
			$tahunbulanpublikasi = "  ";

		if($rs->fields["jenis_pembiayaan"])
			$jenispembiayaan = $rs->fields["jenis_pembiayaan"];
		else
			$jenispembiayaan = "  ";

		if($rs->fields["judul_penelitian1"])
			$judul_penelitian1 = $rs->fields["judul_penelitian1"];
		else
			$judul_penelitian1 = "  ";

		if($rs->fields["judul_penelitian2"])
			$judul_penelitian2 = $rs->fields["judul_penelitian2"];
		else
			$judul_penelitian2 = "  ";		

		if($rs->fields["judul_penelitian3"])
			$judul_penelitian3 = $rs->fields["judul_penelitian3"];
		else
			$judul_penelitian3 = "  ";

		if($rs->fields["judul_penelitian4"])
			$judul_penelitian4 = $rs->fields["judul_penelitian4"];
		else
			$judul_penelitian4 = "  ";

		if($rs->fields["judul_penelitian5"])
			$judul_penelitian5 = $rs->fields["judul_penelitian5"];
		else
			$judul_penelitian5 = "  ";
			
	dbase_add_record($db, array(
		$thnpelaporan,
		$kode_perguruan_tinggi,
		$kode_program_studi,
		$kode_jenjang_pendidikan,
		$nidn,
		$nourut,
		$jenispenelitian,
		$media,
		$pengarang,
		$mandiritim,
		$tahunbulanpublikasi,
		$jenispembiayaan,
		$judul_penelitian1,
		$judul_penelitian2,
		$judul_penelitian3,
		$judul_penelitian4,
		$judul_penelitian5
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
		$rec['tabelpdpt'] = 'tran_publikasi_dosen_tetap';
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