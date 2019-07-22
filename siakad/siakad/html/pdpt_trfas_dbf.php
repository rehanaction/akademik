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
			header('Content-Disposition: attachment; filename="TRFAS.DBF"');	
	}
	
	$strSQL = "select * from tmst_sarana_pt where tahun_pelaporan='$c_periode' ";	
	if($c_unit<>'405016')
		$strSQL .= " and kode_program_studi='$c_unit'";
	$rs = $connp->Execute($strSQL);	
		
	$filedbf = Config::fileDbf."TRFAS.DBF";
	$newfile = Config::fileDbfNew."TRFAS.DBF";
	
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

		if($rs->fields["luas_tanah_institusi"])
			$luas_tanah_institusi = $rs->fields["luas_tanah_institusi"];
		else
			$luas_tanah_institusi = "  ";

		if($rs->fields["luas_kebun_seluruhnya"])
			$luas_kebun_seluruhnya = $rs->fields["luas_kebun_seluruhnya"];
		else
			$luas_kebun_seluruhnya = "  ";

		if($rs->fields["luas_total_ruang_kuliah"])
			$luas_total_ruang_kuliah = $rs->fields["luas_total_ruang_kuliah"];
		else
			$luas_total_ruang_kuliah = "  ";

		if($rs->fields["jumlah_ruang_kuliah"])
			$jumlah_ruang_kuliah = $rs->fields["jumlah_ruang_kuliah"];
		else
			$jumlah_ruang_kuliah = "  ";	
		
		if($rs->fields["luas_total_laboratorium"])
			$luas_total_laboratorium = $rs->fields["luas_total_laboratorium"];
		else
			$luas_total_laboratorium = "  ";	
			
		if($rs->fields["jumlah_ruang_laboratorium"])
			$jumlah_ruang_laboratorium = $rs->fields["jumlah_ruang_laboratorium"];
		else
			$jumlah_ruang_laboratorium = "  ";

		if($rs->fields["luas_ruang__dosen"])
			$luas_ruang__dosen = $rs->fields["luas_ruang__dosen"];
		else
			$luas_ruang__dosen = "  ";

		if($rs->fields["luas_ruang_administrasi"])
			$luas_ruang_administrasi = $rs->fields["luas_ruang_administrasi"];
		else
			$luas_ruang_administrasi = "  ";

		if($rs->fields["luas_ruang__ekskul"])
			$luas_ruang__ekskul = $rs->fields["luas_ruang__ekskul"];
		else
			$luas_ruang__ekskul = "  ";

		if($rs->fields["luas_ruang_seminar"])
			$luas_ruang_seminar = $rs->fields["luas_ruang_seminar"];
		else
			$luas_ruang_seminar = "  ";		

		if($rs->fields["luas_ruang_komputer"])
			$luas_ruang_komputer = $rs->fields["luas_ruang_komputer"];
		else
			$luas_ruang_komputer = "  ";

		if($rs->fields["luas_ruang_perpustakaan"])
			$luas_ruang_perpustakaan = $rs->fields["luas_ruang_perpustakaan"];
		else
			$luas_ruang_perpustakaan = "  ";

		if($rs->fields["jumlah_judul_buku"])
			$jumlah_judul_buku = $rs->fields["jumlah_judul_buku"];
		else
			$jumlah_judul_buku = "  ";

		if($rs->fields["jumlah_eksemplar_buku"])
			$jumlah_eksemplar_buku = $rs->fields["jumlah_eksemplar_buku"];
		else
			$jumlah_eksemplar_buku = "  ";
			
		if($rs->fields["luas_kebun_prodi"])
			$luas_kebun_prodi = $rs->fields["luas_kebun_prodi"];
		else
			$luas_kebun_prodi = "  ";
			
		if($rs->fields["luas_ruang_kuliah_prodi"])
			$luas_ruang_kuliah_prodi = $rs->fields["luas_ruang_kuliah_prodi"];
		else
			$luas_ruang_kuliah_prodi = "  ";
			
		if($rs->fields["jumlah_ruang_kuliah_prodi"])
			$jumlah_ruang_kuliah_prodi = $rs->fields["jumlah_ruang_kuliah_prodi"];
		else
			$jumlah_ruang_kuliah_prodi = "  ";
			
		if($rs->fields["luas_laboratorium_prodi"])
			$luas_laboratorium_prodi = $rs->fields["luas_laboratorium_prodi"];
		else
			$luas_laboratorium_prodi = "  ";

		if($rs->fields["jumlah_laboratorium_prodi"])
			$jumlah_laboratorium_prodi = $rs->fields["jumlah_laboratorium_prodi"];
		else
			$jumlah_laboratorium_prodi = "  ";
		
		if($rs->fields["luas_ruang_dosen_prodi"])
			$luas_ruang_dosen_prodi = $rs->fields["luas_ruang_dosen_prodi"];
		else
			$luas_ruang_dosen_prodi = "  ";
			
		if($rs->fields["luas_ruang_administrasi_prodi"])
			$luas_ruang_administrasi_prodi = $rs->fields["luas_ruang_administrasi_prodi"];
		else
			$luas_ruang_administrasi_prodi = "  ";
		
		if($rs->fields["jumlah_judul_buku_prodi"])
			$jumlah_judul_buku_prodi = $rs->fields["jumlah_judul_buku_prodi"];
		else
			$jumlah_judul_buku_prodi = "  ";
		
		if($rs->fields["jumlah_eksemplar_buku_prodi"])
			$jumlah_eksemplar_buku_prodi = $rs->fields["jumlah_eksemplar_buku_prodi"];
		else
			$jumlah_eksemplar_buku_prodi = "  ";
			
	dbase_add_record($db, array(
		$thnpelaporan,
		$kode_perguruan_tinggi,
		$kode_program_studi,
		$kode_jenjang_pendidikan,
		$luas_tanah_institusi,
		$luas_kebun_seluruhnya,
		$luas_total_ruang_kuliah,
		$jumlah_ruang_kuliah,
		$luas_total_laboratorium,
		$jumlah_ruang_laboratorium,
		$luas_ruang__dosen,
		$luas_ruang_administrasi,
		$luas_ruang__ekskul,
		$luas_ruang_seminar,
		$luas_ruang_komputer,
		$luas_ruang_perpustakaan,
		$jumlah_judul_buku,
		$jumlah_eksemplar_buku,
		$luas_kebun_prodi,
		$luas_ruang_kuliah_prodi,
		$jumlah_ruang_kuliah_prodi,
		$luas_laboratorium_prodi,
		$jumlah_laboratorium_prodi,
		$luas_ruang_dosen_prodi,
		$luas_ruang_administrasi_prodi,
		$jumlah_judul_buku_prodi,
		$jumlah_eksemplar_buku_prodi
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
		$rec['tabelpdpt'] = 'tmst_sarana_pt';
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