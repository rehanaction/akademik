<?php
	include("eps_tbkod.php");
	
	$c_unit = $r_unit;
	$c_periode = $_POST["periode"];	
	$c_thnpelaporan = $_POST["tahun"];	
	$c_kurikulum = $_POST["thnkurikulum"];	
	$tahun = substr($c_periode,0,4);
	
	#diambil per tahun, karena di sim adanya tahun bukan semester
	$sql = "select * from ms_fasilitasprodi where kodeunit='$c_unit' and tahun='$tahun'";
	$rs = $conn->Execute($sql);
	
	// jenjang pendidikan
	// $rs_pendidikan = $connsdm->Execute("select distinct on (nip) nip, rp.idpendidikan,namajenjang from pe_rwtpendidikan rp 
								// left join lv_pendidikan lp on lp.idpendidikan=rp.idpendidikan order by nip,idpendidikan desc");
	// $arr_pendidikan = array();
	// while($row_a = $rs_pendidikan->FetchRow()){
		// $arr_pendidikan[$row_a['nip']] = $row_a['namajenjang'];
	// }
	//program/jenjang studi
	$rs_jenjang = $conn->GetRow("select program,epskodeprodi from ms_unit where kodeunit='$c_unit'");
	
	$jml1=0;$jml2=0;$loop=0;
	while(!$rs->EOF){	
		$idfasilitas = $rs->fields['idfasilitasprodi'];
		$cek = $connp->GetOne("select 1 from tmst_sarana_pt where idfasilitasprodi='$idfasilitas' and tahun_pelaporan='$c_periode'");	
		$record = array();	
		$record['kode_tmst_program_studi'] = null;
		$record['tahun_pelaporan'] = $c_periode;
		$record['kode_perguruan_tinggi'] = "405016";
		$record['kode_jenjang_pendidikan'] = getJenjangEPS($rs_jenjang['program']);
		$record['kode_program_studi'] = $rs_jenjang['epskodeprodi'];
		$record['luas_tanah_institusi'] = $rs->fields['luastanahinstitusi'];
		$record['luas_kebun_seluruhnya'] = $rs->fields['luaskebunseluruhnya'];
		$record['luas_kebun_prodi'] = $rs->fields['luaskebunpercobaan'];
		$record['luas_total_ruang_kuliah'] = $rs->fields['luastotruangkul'];
		$record['luas_total_laboratorium'] = $rs->fields['luastotlaborat'];
		$record['jumlah_ruang_laboratorium'] = $rs->fields['jmlhruanglaborat'];
		
		$record['jumlah_ruang_kuliah'] = $rs->fields['jmlhruangkul'];
		$record['luas_ruang__dosen'] = $rs->fields['luastotruangdosen'];
		$record['luas_ruang_administrasi'] = $rs->fields['luastotruangadmin'];
		$record['luas_ruang_seminar'] = $rs->fields['luastotruangseminar'];
		$record['luas_ruang__ekskul'] = $rs->fields['luastotekskul'];
		$record['luas_ruang_komputer'] = $rs->fields['luastotruangpusatkomp'];
		$record['luas_ruang_perpustakaan'] = $rs->fields['luastotperpus'];
		$record['jumlah_judul_buku'] = $rs->fields['jmlhjudulbuku'];
		$record['jumlah_eksemplar_buku'] = $rs->fields['jmlheksemplarbuku'];
		$record['luas_ruang_kuliah_prodi'] = $rs->fields['luasruangkul'];
		$record['jumlah_ruang_kuliah_prodi'] = $rs->fields['jmlhruangkul'];
		$record['luas_laboratorium_prodi'] = $rs->fields['luaslaborat'];
		$record['jumlah_laboratorium_prodi'] = null;
		$record['luas_ruang_dosen_prodi'] = $rs->fields['luastotruangdosen'];
		$record['luas_ruang_administrasi_prodi'] = $rs->fields['luastotruangadmin'];
		$record['jumlah_judul_buku_prodi'] = $rs->fields['jmlhjudulbuku'];
		$record['jumlah_eksemplar_buku_prodi'] = $rs->fields['jmlheksemplarbuku'];
		$record['idfasilitasprodi'] = $rs->fields['idfasilitasprodi'];
		
		if($cek<>'1'){		
			$jml1++;
			$ok = $connp->AutoExecute('tmst_sarana_pt',$record,'INSERT');
			$message = 'Transfer Data Sukses, sebanyak : '.$jml1;
		}
		else if($cek=='1'){
			$jml2++;
			$ok = $connp->AutoExecute('tmst_sarana_pt',$record,'UPDATE','idfasilitasprodi = "$idfasilitas"',false);
			$message = 'Update Data Sukses, sebanyak : '.$jml2;
		}
		$rs->MoveNext();
		$loop++;
	}
	// if($ok){
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_jenjang['epskodeprodi'];
		$rec['periode'] = $c_periode;
		$rec['thnkurikulum'] = $c_kurikulum;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'tmst_sarana_pt';
		$rec['nip'] = $_SESSION['SIAKAD_USER'];
		$rec['namapetugas'] = $_SESSION['SIAKAD_NAME'];
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		$ok = $connp->AutoExecute('ms_transferpdpt',$rec,'INSERT');	
	// }
	
	if($loop == 0){
	// }else{
		$message = 'Tidak ada data yang ditemukan';
	// }
	}
	
?>