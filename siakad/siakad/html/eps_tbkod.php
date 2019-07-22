<? 
	//proses mapping pada epsbed tabel tbkod.dbf untuk referensinya
	//proses dilakukan secara manual di cocokan dengan yang ada di tabel postgre dan tbkod.dbf
	
	//kode aplikasi 01
	function getPendidikanTertinggiEPS($indic)
	{
		if ($indic == 'S3')
			return 'A'; 
		else if ($indic == 'S2')
			return 'B'; 
		else if ($indic == 'S1')
			return 'C'; 
		else if ($indic == 'SP1')
			return 'D'; 
		else if ($indic == 'SP2')
			return 'E'; 
		else if ($indic == 'D4')
			return 'F'; 	
		else if ($indic == 'D3')
			return 'G'; 
		else if ($indic == 'D2')
			return 'H'; 
		else if ($indic == 'D1')
			return 'I'; 
		else if ($indic == 'PROFESI')
			return 'J'; 
		else 
			return '';
	}
	
	//kode aplikasi 02
	function getJabatanEPS($indic)
	{
		if ($indic == 'Tenaga Pengajar' or $indic == '2200')
			return 'A'; 
		else if ($indic == 'Asisten Ahli' or $indic == '2201' or $indic == '2202')
			return 'B'; 
		else if ($indic == 'Lektor' or $indic == '2203' or $indic == '2204' or $indic == '2205')
			return 'C'; 
		else if ($indic == 'Lektor Kepala' or $indic == '2206' or $indic == '2207')
			return 'D'; 
		else if ($indic == 'Guru Besar' or $indic == '2208' or $indic == '2209')
			return 'E'; 
		else 
			return '';
		// if ($indic == 'Tenaga Pengajar')
			// return 'A'; 
		// else if ($indic == 'Asisten Ahli')
			// return 'B'; 
		// else if ($indic == 'Lektor')
			// return 'C'; 
		// else if ($indic == 'Lektor Kepala')
			// return 'D'; 
		// else if ($indic == 'Guru Besar')
			// return 'E'; 
		// else 
			// return '';
			
	}
	
	//kode aplikasi 03
	function getStatusIkatanDosen($indic){
		if($indic == 'Dosen Tetap' or $indic == '1')//PNS
			return 'A';
		else if($indic == 'Dosen PNS DPK')
			return 'B';
		else if($indic == 'Dosen PNS PTN')
			return 'C';	
		else if($indic == 'Honorer Non-PNS PTN' or $indic == '3')//honorer
			return 'D';
		else if($indic == 'Kontrak / Tetap Kontrak' or $indic == '2')//cpns
			return 'E';
		// if($indic == 'Dosen Tetap')
			// return 'A';
		// else if($indic == 'Dosen PNS DPK')
			// return 'B';
		// else if($indic == 'Dosen PNS PTN')
			// return 'C';	
		// else if($indic == 'Honorer Non-PNS PTN')
			// return 'D';
		// else if($indic == 'Kontrak / Tetap Kontrak')
			// return 'E';
	}
	
	//kode aplikasi 04
	function getJenjangEPS($indic)
	{
		if ($indic == 'S3')
			return 'A'; 
		else if ($indic == 'S2')
			return 'B'; 
		else if ($indic == 'S1')
			return 'C'; 
		else if ($indic == 'D4')
			return 'D'; 
		else if ($indic == 'D3')
			return 'E'; 
		else if ($indic == 'D2')
			return 'F'; 	
		else if ($indic == 'D1')
			return 'G'; 
		else if ($indic == 'SP1')
			return 'H'; 
		else if ($indic == 'SP2')
			return 'I'; 
		else if ($indic == 'PROFESI')
			return 'J'; 
		else 
			return '';
	}
	
	//kode aplikasi 05
	function getStatusMhsEPS($indic)
	{
		if ($indic == 'A')
			return 'A'; //status aktif
		else if ($indic == 'C')
			return 'C'; // cuti
		else if ($indic == 'O')
			return 'D'; // drop out
		else if ($indic == 'U')
			return 'K'; // keluar
		else if ($indic == 'L')
			return 'L'; // lulus
		else if ($indic == 'T')
			return 'N'; // non aktif
		else 
			return '';
	}
	
	//kode aplikasi 06	
	function getStsMhsMasukEPS($ptasal)
	{
		if (kosong($ptasal))
			return 'B'; //mhs baru
		else 
			return 'P'; //mhs pindahan
	}
	
	//kode aplikasi 07
	function getStatusAkreditasiEPS($indic)
	{
		if ($indic == 'A')
			return 'A'; 
		else if ($indic == 'B')
			return 'B'; 
		else if ($indic == 'C')
			return 'C'; 
		else if ($indic == 'D')
			return 'D'; 
		else if ($indic == 'U')
			return 'U'; 
		else if ($indic == 'L')
			return 'L'; 
		else
			return '';
	}
	
	//kode aplikasi 08
	function getJenisKelamin($indic)
	{
		if ($indic == 'L')
			return 'L'; 
		else if ($indic == 'P')
			return 'P'; 
		else
			return '';
	}
	
	//kode aplikasi 10	
	function getKelMatkulEPS($indic)
	{
		if ($indic == 'MPK') 
			return 'A'; 
		else if ($indic == 'MKK')
			return 'B'; 
		else if ($indic == 'MKB')
			return 'C'; 
		else if ($indic == 'MPB')
			return 'D'; 
		else if ($indic == 'MBB')
			return 'E'; 
		else if (($indic == 'MKU') or ($indic == 'MKDU'))
			return 'F'; 
		else if ($indic == 'MKDK')
			return 'G'; 
		else if ($indic == 'MKK')
			return 'H'; 
		else
			return '';
	}
	
	//kode aplikasi 11
	function getJenisKurikulumEPS($indic)
	{
		if ($indic == 'W') 
			return 'A'; 
		else if ($indic == 'P')
			return 'B'; 
		else
			return '';
	}
	
	//kode aplikasi 14
	function getStatusProgStudiEPS($indic)
	{
		if ($indic == 'A') 
			return 'A'; // aktif
		else if ($indic == 'H')
			return 'H'; // hapus
		else if ($indic == 'N')
			return 'N'; // non aktif
		else
			return '';
	}
	
	//kode aplikasi 15
	function getStatusAktDosenEPS($indic)
	{
		if ($indic == 'AK')
			return 'A'; //aktif
		else if ($indic == 'CB' or $indic == 'CL' or $indic == 'CP')
			return 'C'; //cuti
		else if ($indic == 'KL' or $indic == 'N')
			return 'K'; //keluar
		else if ($indic == 'SR' or $indic == 'SI' or $indic == 'KS' or $indic == 'S')
			return 'S'; //studi lanjut
		else if ($indic == 'MT')
			return 'M'; //wafat
		else if ($indic == 'PD' or $indic == 'PT' or $indic == 'PS')
			return 'P'; //pensiun
		else if ($indic == 'ID' or $indic == 'IP' or $indic == '2I' or $indic == '2L' or $indic == '2N' or $indic == '3I' or $indic == '3L' or $indic == '3N')
			return 'T'; //tugas di instansi lain
		else 
			return '';	
		// if ($indic == 'A')
			// return 'A'; //aktif
		// else if ($indic == 'C')
			// return 'C'; //cuti
		// else if ($indic == 'K' or $indic == 'N')
			// return 'K'; //keluar
		// else if ($indic == 'S')
			// return 'S'; //studi lanjut
		// else if ($indic == 'W')
			// return 'M'; //wafat
		// else if ($indic == 'P')
			// return 'P'; //pensiun
		// else if ($indic == 'T')
			// return 'T'; //tugas di instansi lain
		// else 
			// return '';			
	}

	// kode aplikasi 18
	function getHariKuliahEPS($indic)
	{
		if ($indic == '1') 
			return 'A'; // senin - Jumat atau Sabtu 
		else if ($indic == '2')
			return 'B'; // sabtu dan minggu
		else if ($indic == '3')
			return 'C'; // sabtu atau minggu saja
		else
			return '';
	}
	
	// kode aplikasi 19
	function getPelaksanaanSemPendekEPS($indic)
	{
		if ($indic == 'A') 
			return 'A'; // hanya perbaikan nilai
		else if ($indic == 'B')
			return 'B'; // perbaikan nilai dan baru
		else
			return '';
	}
	
	// kode aplikasi 24
	function getJenisPenelitian($indic)
	{
		if ($indic == 'PENELITIAN')
			return 'A';
		else
			return 'B';
	}
	
	// kode aplikasi 25
	function getMediaPublikasi($indic)
	{
		if ($indic == 'BUKU' OR $indic == 'ARTIKELBUKU')
			return 'H';
		else if ($indic == 'JURNAL')
			return 'E';
		else if ($indic == 'SEMINAR')
			return 'B';
		else if ($indic == 'SURATKABAR')
			return 'A';
		else
			return '';
	}
	
	//kode aplikasi 28
	function getJenisMatkulEPS($indic)
	{
		if ($indic == 'W')
			return 'A'; //Wajib
		else if ($indic == 'P')
			return 'B'; //Pilihan 
	/* 	else if($indic == 'WP')
			return 'C'; //Wajib Peminatan
		else if($indic == 'PP')
			return 'D'; //Pilihan Peminatan
		else if($indic == 'TA')
			return 'S'; //Tugas Akhir / tesis */
		else return '';
	}
	
	//kode aplikasi 32
	function getStatusMatkulEPS($indic)
	{
		if ($indic == 'A')
			return 'A'; // Aktif
		else if ($indic == 'H')
			return 'H'; //Hapus 	
		else return '';
	}	
	
?>