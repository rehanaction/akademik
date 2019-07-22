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
			header('Content-Disposition: attachment; filename="TRKAP.DBF"');	
	}
	
	$strSQL = "select * from tran_daya_tampung where tahun_pelaporan like '".$c_thnpelaporan."%'";	
	if($c_unit<>'405016')
		$strSQL .= " and kode_program_studi='$c_unit'";
	$rs = $connp->Execute($strSQL);	
		
	$filedbf = Config::fileDbf."TRKAP.DBF";
	$newfile = Config::fileDbfNew."TRKAP.DBF";
	
	copy($filedbf, $newfile);

	$db = dbase_open($newfile,2);

	if ($db) {
	while (!$rs->EOF) 
	{	$i++;
		
		if($rs->fields["tahun_pelaporan"])
			$tahunpelaporan = $rs->fields["tahun_pelaporan"];
		else
			$tahunpelaporan = "  ";
			
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

		if($rs->fields["target_mhs_baru"])
			$target = $rs->fields["target_mhs_baru"];
		else
			$target = 0;

		if($rs->fields["calon_ikut_seleksi"])
			$calonseleksi = $rs->fields["calon_ikut_seleksi"];
		else
			$calonseleksi = 0;

		if($rs->fields["calon_lulus_seleksi"])
			$calonlulus = $rs->fields["calon_lulus_seleksi"];
		else
			$calonlulus = 0;

		if($rs->fields["mendaftar_sebagai_mahasiswa"])
			$mendaftarmhs = $rs->fields["mendaftar_sebagai_mahasiswa"];
		else
			$mendaftarmhs = 0;	
		
		if($rs->fields["peserta_mengundurkan_diri"])
			$mengundurkandiri = $rs->fields["peserta_mengundurkan_diri"];
		else
			$mengundurkandiri = 0;	
			
		if($rs->fields["jml_mhs_pindahan"])
			$pindahan = $rs->fields["jml_mhs_pindahan"];
		else
			$pindahan = 0;

		if($rs->fields["tgl_awal_kuliah_ganjil"])
			$awalkulganjil = $rs->fields["tgl_awal_kuliah_ganjil"];
		else
			$awalkulganjil = "  ";

		if($rs->fields["tgl_akhir_kuliah_ganjil"])
			$akhirkulganjil = $rs->fields["tgl_akhir_kuliah_ganjil"];
		else
			$akhirkulganjil = "  ";

		if($rs->fields["jml_minggu_kuliah_ganjil"])
			$jmlmingguganjil = $rs->fields["jml_minggu_kuliah_ganjil"];
		else
			$jmlmingguganjil = 0;

		if($rs->fields["tgl_awal_kuliah_genap"])
			$awalkulgenap = $rs->fields["tgl_awal_kuliah_genap"];
		else
			$awalkulgenap = "  ";		

		if($rs->fields["tgl_akhir_kuliah_genap"])
			$akhirkulgenap = $rs->fields["tgl_akhir_kuliah_genap"];
		else
			$akhirkulgenap = "  ";

		if($rs->fields["jml_minggu_kuliah_genap"])
			$jmlminggugenap = $rs->fields["jml_minggu_kuliah_genap"];
		else
			$jmlminggugenap = "  ";

		if($rs->fields["metode_kuliah"])
			$metode = $rs->fields["metode_kuliah"];
		else
			$metode = "  ";

		if($rs->fields["kelas_ekstensi"])
			$ekstensi = $rs->fields["kelas_ekstensi"];
		else
			$ekstensi = "  ";
			
		if($rs->fields["metode_kuliah_ekstensi"])
			$metodeekstensi = $rs->fields["metode_kuliah_ekstensi"];
		else
			$metodeekstensi = "  ";
			
		if($rs->fields["kegiatan_sp"])
			$kegiatansp = $rs->fields["kegiatan_sp"];
		else
			$kegiatansp = "  ";
			
		if($rs->fields["jml_sp_setahun"])
			$jmlspsetahun = $rs->fields["jml_sp_setahun"];
		else
			$jmlspsetahun = "  ";
			
		if($rs->fields["metode_sp"])
			$metodesp = $rs->fields["metode_sp"];
		else
			$metodesp = "  ";

	dbase_add_record($db, array(
		$tahunpelaporan,
		$kode_perguruan_tinggi,
		$kode_program_studi,
		$kode_jenjang_pendidikan,
		$target,
		$calonseleksi,
		$calonlulus,
		$mendaftarmhs,
		$mengundurkandiri,
		$pindahan,
		$awalkulganjil,
		$akhirkulganjil,
		$jmlmingguganjil,
		$awalkulgenap,
		$akhirkulgenap,
		$jmlminggugenap,
		$metode,
		$ekstensi,
		$metodeekstensi,
		$kegiatansp,
		$jmlspsetahun,
		$metodesp
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
		$rec['tabelpdpt'] = 'tran_daya_tampung';
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