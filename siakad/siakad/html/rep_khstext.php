<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporanmhs'));
	$conn->debug = true;
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_angkatan = (int)$_REQUEST['angkatan'];
	$r_format = $_REQUEST['format'];
	
	if(Akademik::isMhs())
		$r_npm = Modul::getUserName();
	else
		$r_npm = CStr::removeSpecial($_REQUEST['npm']);
	
	// $r_periode = Akademik::getPeriode();
	$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	print_r($r_periode);
	// properti halaman
	$p_title = 'Laporan KHS';
	$p_tbwidth = 720;
	
	if(empty($r_npm)) {
		$p_namafile = 'khs_'.$r_periode.'_'.$r_kodeunit.'_'.$r_angkatan;
		$a_data = mLaporanMhs::getKHSUnit($conn,$r_periode,$r_kodeunit,$r_angkatan);
	}
	else {
		$p_namafile = 'khs_'.$r_npm;
		$a_data = mLaporanMhs::getKHS($conn,$r_periode,$r_npm);
	}
	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
	
?>
<?php
$tmpdir = sys_get_temp_dir(); # ambil direktori temporary untuk simpan file. 
$file = tempnam($tmpdir, 'ctk'); # nama file temporary yang akan dicetak 
$handle = fopen($file, 'w'); 
$condensed = Chr(27) . Chr(33) . Chr(4); 
$bold1 = Chr(27) . Chr(69); 
$bold0 = Chr(27) . Chr(70); 
$initialized = chr(27).chr(64); 
$condensed1 = chr(15); 
$condensed0 = chr(18);
$Data = $initialized; 
$Data .= $condensed1; 
	
$data = "UNIVERSITAS ESA UNGGUL  \n";
$data .= strtoupper($rs->fields["namaunit"])." \n";
$data .= "Kampus A : Jl. SMEA No. 57 Jakarta\n";
$data .= "Kampus B : Jl. Raya Jemursari 51-57 Jakarta\n";
$data .= "Telp. (031) 8291920 - 8284508 Fax. (031) 8291920 Website: http://.esaunggul.ac.id \n";
$data .= "===============================================================================================================================\n"; 
$data .="KARTU HASIL STUDI (KHS)\n";
$data.= Akademik::getNamaPeriode($r_periode)."\n";

$m = count($a_data);
for($c=0;$c<$m;$c++) {
	$len_nim = 50-(5+strlen($row['nim']));
	$len_nama = 50-(6+strlen($$row['nama']));
	
	$row = $a_data[$c];
	$data.="NIM: ".$row['nim'].str_repeat(' ',$len_nim);
	$data.="Prodi:".$row['namaunit']."\n";
	$data.="Nama: ".$row['nama'].str_repeat(' ',$len_nama);
	$data.="Semester :".$row['semestermhs']."\n";
	$data.="-------------------------------------------------------------------------------------------------------------------------------\n";
	$data.="|No.\t|Mata Kuliah".str_repeat(' ',30)."|Kode MK\t|SKS(K)\t|Nilai Huruf\t|Nilai Angka\t|Jumlah\t|\n";
	$data.="-------------------------------------------------------------------------------------------------------------------------------\n";
	
	$t_tnsks = 0; $jml_sks = 0;
	$n = count($row['khs']);
	for($i=0;$i<$n;$i++) {
		$rowk = $row['khs'][$i];
		
		if(empty($rowk['nilaimasuk'])) {
			$t_nhuruf = '';
			$t_nangka = 0;
		}
		else {
			$t_nhuruf = $rowk['nhuruf'];
			$t_nangka = $rowk['nangka'];
		}
		
		$t_nsks = $t_nangka*$rowk['sks'];
		$t_tnsks += $t_nsks;
		$jml_sks = $jml_sks+$rowk['sks'];
		
		$sisa_len_namamk = 41-strlen($rowk['namamk']);
		$data .= "|".($i+1).".\t\t|".$rowk['namamk'].str_repeat(' ',$sisa_len_namamk)."|".$rowk['kodemk']."\t|".$rowk['sks']."\t\t|".$t_nhuruf."\t\t\t\t|".$t_nangka."\t\t\t|".$t_nsks."\t\t|\n";
	}
	$data .= "-------------------------------------------------------------------------------------------------------------------------------\n";
	$data .= "Jumlah\t\t\t\t\t\t\t\t\t\t\t\t\t\t|".$jml_sks."\t\t\t\t\t\t\t\t\t\t|".$t_tnsks."\n";
	$data .= "IPS Semester ".$row['semestermhs']."\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t|".$row['ips']."\n";
	$data .= "IPS Semester Lalu ".($row['semestermhs']-1)."\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t|".$row['ips']."\n";
	$data .= "Total SKS Lulus ".($row['semestermhs']-1)."\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t|".$row['skslulus']."\n";
	$data .= "IPK Kumulatif ".($row['semestermhs']-1)."\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t|".$row['ipk']."\n";
	$data .= "-------------------------------------------------------------------------------------------------------------------------------\n";
	
	$data .= "Predikat: MEMUASKAN\t\t\t\t\t\t\t\t\t\t\t\t\t\tJakarta,".date('d-m-Y')."\n\n";
	$data .= "Catatan pembimbing Akademik\n";
	$data .= "----------------------------\n";
	$data .= "--------------------------------------\n";
	$data .= "--------------------------------------\n";
	$data .= "--------------------------------------\n";
	$data .= "--------------------------------------\n";
	
	$data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tMengetahui:\n ";
	$data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tKetua ".$row['namaunit']."\n\n";
	$data .= "Keterangan:\n";
	$data .= "1. Warna Putih: Mahasiswa\n";
	$data .= "2. Warna Biru: Pembimbing Akademik\n";
	$data .= "3. Warna Merah: Administrasi Akademik\t\t\t\t\t\t\t\t\t NUR ZUWARIYAH, S.ST\n";
	
	// echo $data;
	fwrite($handle, $data); 
	fclose($handle); 
	$rs_ip = $conn->GetRow("select * from ms_setting limit 1");
	copy($file, $rs_ip['ip_dotmetrix'].'/'.$rs_ip['nama_printer']); # alamat printer share & Lakukan cetak 
	unlink($file); 
	
}
?>