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
			header('Content-Disposition: attachment; filename="TRFAS.csv"');	
	}
  
	$tableTitle = ".: Data Transaksi Fasilitas Prodi :.";
	$columnCount = 33;
	
	$strSQL = "select * from tmst_sarana_pt where tahun_pelaporan='$c_periode' ";	
	if($c_unit<>'405016')
		$strSQL .= " and kode_program_studi='$c_unit'";
	
	$rs = $connp->Execute($strSQL);	
	echo 'THSMSTRFAS,KDPTITRFAS,KDPSTTRFAS,KDJENTRFAS,LSTNHTRFAS,LSBUNTRFAS,RGKULTRFAS,JRKULTRFAS,RGLABTRFAS,JRLABTRFAS,RGDOSTRFAS,RGADMTRFAS,RGMHSTRFAS,RGSEMTRFAS,RGKOMTRFAS,RGPUSTRFAS,JDBUKTRFAS,JMBUKTRFAS,LSBUPTRFAS,RGKUPTRFAS,JRKUPTRFAS,RGLAPTRFAS,JRLAPTRFAS,RGDOPTRFAS,RGADPTRFAS,JDBUPTRFAS,JMBUPTRFAS'."\n";		  
	while (!$rs->EOF) 
	{	$i++;
		echo '"'.$rs->fields["tahun_pelaporan"].'","'.$rs->fields["kode_perguruan_tinggi"].'",'.
			 '"'.$rs->fields["kode_program_studi"].'","'.$rs->fields["kode_jenjang_pendidikan"].'",'.
			 '"'.$rs->fields["luas_tanah_institusi"].'","'.$rs->fields["luas_kebun_seluruhnya"].'",'.
			 '"'.$rs->fields["luas_total_ruang_kuliah"].'","'.$rs->fields["jumlah_ruang_kuliah"].'",'.
			 '"'.$rs->fields["luas_total_laboratorium"].'","'.$rs->fields["jumlah_ruang_laboratorium"].'",'.
			 '"'.$rs->fields["luas_ruang__dosen"].'","'.$rs->fields["luas_ruang_administrasi"].'",'.
			 '"'.$rs->fields["luas_ruang__ekskul"].'","'.$rs->fields["luas_ruang_seminar"].'",'.
			 '"'.$rs->fields["luas_ruang_komputer"].'","'.$rs->fields["luas_ruang_perpustakaan"].'",'.
			 '"'.$rs->fields["jumlah_judul_buku"].'","'.$rs->fields["jumlah_eksemplar_buku"].'",'.
			 '"'.$rs->fields["luas_kebun_prodi"].'","'.$rs->fields["luas_ruang_kuliah_prodi"].'",'.
			 '"'.$rs->fields["jumlah_ruang_kuliah_prodi"].'","'.$rs->fields["luas_laboratorium_prodi"].'",'.
			 '"'.$rs->fields["jumlah_laboratorium_prodi"].'","'.$rs->fields["luas_ruang_dosen_prodi"].'",'.
			 '"'.$rs->fields["luas_ruang_administrasi_prodi"].'","'.$rs->fields["jumlah_judul_buku_prodi"].'",'.
			 '"'.$rs->fields["jumlah_eksemplar_buku_prodi"].'"'."\n";
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
		$rec['tabelpdpt'] = 'tmst_sarana_pt';
		$rec['nip'] = $_SESSION['SIAKAD_USER'];
		$rec['namapetugas'] = $_SESSION['SIAKAD_NAME'];
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		$rec['format'] = $format; 
		$ok = $connp->AutoExecute('ms_downloadpdpt',$rec,'INSERT');	
	}
	
	if($filter=='all') {
		$file = 'pdpt/TRFAS.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>