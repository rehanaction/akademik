<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	$conn->debug = true;
	// variabel request
	if(Akademik::isMhs())
		$c_nim = Modul::getUserName();
	else
		$c_nim = CStr::removeSpecial($_REQUEST['npm']);
	
	//$conn->Execute("update ak_perwalian set frsdisetujui=-1 where nim='$c_nim' and periode='" . $_SESSION["SIA_PERIODE"] . "'");
	
//####################
	$c_periode = Akademik::getPeriode();
	
	$SQL	= "select a.thnkurikulum, a.kodeunit, a.kodemk, a.kelasmk, b.namamk from akademik.ak_krs a, akademik.ak_matakuliah b 
				where a.nim='$c_nim' and a.periode='$c_periode' and a.kodemk=b.kodemk and a.thnkurikulum=b.thnkurikulum";
				   
	$rsSQL	= $conn->Execute($SQL);
	while(!$rsSQL->EOF)
	{	
		$strSQL = 	"select akademik.f_cekkres('".$rsSQL->fields["thnkurikulum"]."','$c_periode','".$rsSQL->fields["kodeunit"]."',
					'".$rsSQL->fields["kodemk"]."','".$rsSQL->fields["kelasmk"]."','$c_nim');";
		$kres	=	$conn->GetOne($strSQL);
		
		if (!empty($kres)) 
		{
			$kres = $rsSQL->fields["namamk"]." kres dengan ".$kres;
			$count++;
		 
			$infokres .= '<b>XXX '. $kres .' XXX</b><br />';
		}
		$rsSQL->MoveNext();
	}
 	if(!empty($infokres))
		$infokres .= '<b>--- KRS HARAP DIPERBAIKI ---<b />';

 // Data aktivitas perwalian mahasiswa
 $strPerwalian = "select statusmhs, frsterisi,frsdisetujui,prasyaratspp,t_updatetime from akademik.ak_perwalian where nim='$c_nim' and periode='$c_periode'";
 $rsPerwalian  = $conn->Execute($strPerwalian);
 if (!$rsPerwalian->EOF) {
 	$c_statuskuliah = $rsPerwalian->fields["statusmhs"];
 	$c_frsterisi = $rsPerwalian->fields["frsterisi"];
 	$c_frsdisetujui = $rsPerwalian->fields["frsdisetujui"];
	$c_prasyaratspp = $rsPerwalian->fields["prasyaratspp"];
	$c_t_updatetime = $rsPerwalian->fields["t_updatetime"];
 }
 else 
 {
	$c_statuskuliah = 'A';
	$c_frsterisi = 0;
	$c_frsdisetujui = 0;
	$c_prasyaratspp = 0;
 }


 // if ($c_prasyaratspp == 0) {
		// exit;
 // }  

	$strSQL = "select * from akademik.r_frsnow where nim='$c_nim' and periode='$c_periode' order by kodemk,kelasmk ";
	$rs = $conn->Execute($strSQL);
	
	if(!$rs->EOF)
	{
		$c_nip=$rs->fields["nip"];
		$c_namadosen=$rs->fields["namadosen"];
		$c_nama=$rs->fields["nama"];
		$c_kodeunit=$rs->fields["kodeunit"];
	}
	else
	{
		$strInfo = "select m.nim, m.nama, m.kodeunit, u.namaunit, m.nipdosenwali, akademik.f_namalengkap(d.tmp_gelar1, d.namadepan,d.namatengah,d.namabelakang,d.tmp_gelar2) as namadosen
					from akademik.ms_mahasiswa m left join sdm.ms_pegawai d on (m.nipdosenwali=d.nik) 
					left join gate.ms_unit u on (m.kodeunit=u.kodeunit) where m.nim='$c_nim'";
		$rsInfo  = $conn->Execute($strInfo);
		if (!$rsInfo->EOF) {
			$c_nama = CStr::cStrNull($rsInfo->fields["namadepan"].' '.$rsInfo->fields["namatengah"].' '.$rsInfo->fields["namabelakang"]);	
			$c_kodeunit = $rsInfo->fields["kodeunit"];
			$rs->fields["namaunit"] = $rsInfo->fields["namaunit"];	
			$c_nip = $rsInfo->fields["nipdosenwali"];	
			$c_namadosen =  CStr::cStrNull($rsInfo->fields["namadosen"]);	
			$rs->fields["periode"]=$_SESSION["SIA_PERIODE"];
		}
	}
	
	$c_namafakultas = Akademik::getNamaParentUnit($conn,$c_kodeunit);
	list(,$c_namafakultas) = explode(' ',$c_namafakultas);
	$c_namafakultas = strtoupper($c_namafakultas);
?>
	
<?php	
	//cetak dot matrix ============================================================================================
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
	
	$str = "UNIVERSITAS ESA UNGGUL  \n";
	$str .= strtoupper($rs->fields["namaunit"])." \n";
	$str .= "Kampus A : Jl. SMEA No. 57 Jakarta\n";
	$str .= "Kampus B : Jl. Raya Jemursari 51-57 Jakarta\n";
	$str .= "Telp. (031) 8291920 - 8284508 Fax. (031) 8291920 Website: http://.esaunggul.ac.id \n";
	$str .= "===============================================================================================================================\n"; 
	$str .= "\t\t\t\t\t\tKartu Rencana Studi (KRS)\n";
	$str .= "\t\t\t\t\t(".Akademik::getNamaPeriode($rs->fields["periode"]).")\n\n";
	
	$len_nim = 50-(5+strlen($c_nim));
	$len_nama = 50-(6+strlen($c_nama));
	$str .= "NIM: ".$c_nim.str_repeat(' ',$len_nim)."Jurusan: ".$rs->fields["namaunit"]."\n";
	$str .= "Nama: ".$c_nama.str_repeat(' ',$len_nama)."Semester: ".$rs->fields["semestermhs"]."\n";
	
	
	$str.="-------------------------------------------------------------------------------------------------------------------------------\n";
	$str.="|No.\t|Kode\t\t|Nama Mata Kuliah".str_repeat(' ',20)."|Kelas\t\t|SKS\t|Dosen Pengajar ".str_repeat(' ',35)."\n";
	$str.="-------------------------------------------------------------------------------------------------------------------------------\n";
	
	$count = 0;
	$jumlahsks=0;
	if ($rs->EOF) 
		$str .= "|".str_repeat("\t",8)."KRS masih kosong".str_repeat("\t",8)."|";
	else
	while (!$rs->EOF) {
	$count++;
	$t_kodeunit = $rs->fields["kodeunit"];
	$t_kodemk = $rs->fields["kodemk"];
	$t_namamk = $rs->fields["namamk"];
	$t_kelasmk = $rs->fields["kelasmk"];
	$t_sks = $rs->fields["sks"];
	$t_namahari = $rs->fields["namahari"];
	$t_waktumulai = $rs->fields["waktumulai"];
	$t_waktuselesai = $rs->fields["waktuselesai"];
	$t_namahari2 = $rs->fields["namahari2"];
	$t_waktumulai2 = $rs->fields["waktumulai2"];
	$t_waktuselesai2 = $rs->fields["waktuselesai2"];
	$jumlahsks += $t_sks;
	
	if($rs->fields["namapengajar"]){ 
		$dosen = $rs->fields["namapengajar"]; 
	}else
		$dosen="";
	
	$sisa_len_namamk = 36-strlen($t_namamk);
	$sisa_len_namadosen = 69-strlen($c_namadosen);
	$str .="|".$count.".\t\t|".$t_kodemk."\t\t|".$t_namamk.str_repeat(' ',$sisa_len_namamk)."|".$t_kelasmk."\t\t\t|".$t_sks."\t\t|".$dosen.str_repeat(' ',$sisa_len_namadosen)."\n";
		
	$rs->MoveNext();
	}
	$str.="-------------------------------------------------------------------------------------------------------------------------------\n";
	$str .= "|\t\t\t\t\t\t\t\tTotal SKS yang diambil\t\t\t\t|".$jumlahsks."\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n";
	$str.="-------------------------------------------------------------------------------------------------------------------------------\n\n";
	
	$str .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\tJakarta, \n\n\n";
	$str .= "\t\tPersetujuan Dosen Wali,\t\t\t\t\t\tTanda Tangan Ybs, \n\n\n\n\n\n";
	$str .= "\t\t(".$c_namadosen.")\t\t\t\t\t\t(".$c_nama.") \n";
	$str .= str_repeat("\t",13).$c_nim." \n";
	
	// echo $str;
	
	fwrite($handle, $str); 
	fclose($handle); 
	$rs_ip = $conn->GetRow("select * from akademik.ms_setting limit 1");
	copy($file, $rs_ip['ip_dotmetrix'].'/'.$rs_ip['nama_printer']); # alamat printer share & Lakukan cetak 
	unlink($file); 
?>
