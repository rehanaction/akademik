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
			header('Content-Disposition: attachment; filename="MSPST.DBF"');	
	}
	
	$strSQL = "select * from tmst_program_studi where 1=1 ";	
	if($c_unit<>'405016')
		$strSQL .= " and kode_program_studi='$c_unit'";
	$rs = $connp->Execute($strSQL);	
		
	$filedbf = Config::fileDbf."MSPST.DBF";
	$newfile = Config::fileDbfNew."MSPST.DBF";
	
	copy($filedbf, $newfile);

	$db = dbase_open($newfile,2);

	if ($db) {
	while (!$rs->EOF) 
	{	$i++;
			
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

		if($rs->fields["nama_program_studi"])
			$namaprodi = $rs->fields["nama_program_studi"];
		else
			$namaprodi = "  ";

		if($rs->fields["semester_awal"])
			$semesterawal = $rs->fields["semester_awal"];
		else
			$semesterawal = "  ";

		if($rs->fields["no_sk_dikti"])
			$skdikti = $rs->fields["no_sk_dikti"];
		else
			$skdikti = "  ";

		if($rs->fields["tgl_sk_dikti"])
			$tglskdikti = Helper::formatDateEpsbed($rs->fields["tgl_sk_dikti"]);
		else
			$tglskdikti = date("00010101");	
		
		if($rs->fields["tgl_akhir_sk_dikti"])
			$tglakhirskdikti = Helper::formatDateEpsbed($rs->fields["tgl_akhir_sk_dikti"]);
		else
			$tglakhirskdikti = date("00010101");	
			
		if($rs->fields["sks_lulus"])
			$skslulus = $rs->fields["sks_lulus"];
		else
			$skslulus = 0;

		if($rs->fields["status_program_studi"])
			$statusprodi = $rs->fields["status_program_studi"];
		else
			$statusprodi = "  ";

		if($rs->fields["email"])
			$email = $rs->fields["email"];
		else
			$email = "  ";

		if($rs->fields["tgl_berdiri"])
			$tglberdiri = Helper::formatDateEpsbed($rs->fields["tgl_berdiri"]);
		else
			$tglberdiri = date("00010101");

		if($rs->fields["no_sk_ban"])
			$noskbanpt = $rs->fields["no_sk_ban"];
		else
			$noskbanpt = "  ";		

		if($rs->fields["tgl_sk_ban"])
			$tglskbanpt = Helper::formatDateEpsbed($rs->fields["tgl_sk_ban"]);
		else
			$tglskbanpt = date("00010101");

		if($rs->fields["tgl_akhir_sk_ban"])
			$tglakhirskbanpt = Helper::formatDateEpsbed($rs->fields["tgl_akhir_sk_ban"]);
		else
			$tglakhirskbanpt = date("00010101");

		if($rs->fields["kode_akreditasi"])
			$kodeakreditasi = $rs->fields["kode_akreditasi"];
		else
			$kodeakreditasi = "  ";

		if($rs->fields["frekuensi_kurikulum"])
			$frekuensikurikulum = $rs->fields["frekuensi_kurikulum"];
		else
			$frekuensikurikulum = "  ";
			
		if($rs->fields["pelaksanaan_kurikulum"])
			$pelaksanaankurikulum = $rs->fields["pelaksanaan_kurikulum"];
		else
			$pelaksanaankurikulum = "  ";
			
		if($rs->fields["nidn"])
			$nidn = $rs->fields["nidn"];
		else
			$nidn = "  ";
			
		if($rs->fields["hp_ketua"])
			$hpketua = $rs->fields["hp_ketua"];
		else
			$hpketua = "  ";
			
		if($rs->fields["telepon_kantor"])
			$teleponkantor = $rs->fields["telepon_kantor"];
		else
			$teleponkantor = "  ";

		if($rs->fields["fax"])
			$fax = $rs->fields["fax"];
		else
			$fax = "  ";
		
		if($rs->fields["nama_operator"])
			$namaoperator = $rs->fields["nama_operator"];
		else
			$namaoperator = "  ";
			
		if($rs->fields["hp_operator"])
			$hpoperator = $rs->fields["hp_operator"];
		else
			$hpoperator = "  ";
		
		$mlsem="  ";
	dbase_add_record($db, array(
		$kode_perguruan_tinggi,
		$kode_program_studi,
		$kode_jenjang_pendidikan,
		$namaprodi,
		$semesterawal,
		$skdikti,
		$tglskdikti,
		$tglakhirskdikti,
		$skslulus,
		$statusprodi,
		$mlsem,
		$email,
		$tglberdiri,
		$noskbanpt,
		$tglskbanpt,
		$tglakhirskbanpt,
		$kodeakreditasi,
		$frekuensikurikulum,
		$pelaksanaankurikulum,
		$nidn,
		$hpketua,
		$teleponkantor,
		$fax,
		$namaoperator,
		$hpoperator
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
		$rec['tabelpdpt'] = 'tmst_program_studi';
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