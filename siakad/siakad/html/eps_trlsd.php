<?php
	//dosen cuti/studi lanjut atau meninggal
	include("eps_tbkod.php");
	$connsdm = Factory::getConnSDM();
	
	$c_unit = $r_unit;
	$c_periode = $_POST["periode"];	
	$c_thnpelaporan = $_POST["tahun"];	
	$c_kurikulum = $_POST["thnkurikulum"];	
	
	#diambil per tahun, karena di sim adanya tahun bukan semester
	$sql = "select p.*  from ms_pegawai p 
			left join lv_statusaktif ls on ls.idstatusaktif=p.idstatusaktif 
			left join lv_statuspeg sp on sp.idstatuspegawai=p.idstatuspegawai
			where p.idjenispegawai='ID0' and p.idstatusaktif <> 'AK'";
	if($c_unit<>'000000')
		$sql .= " and p.idsatker='$c_unit'";
	$rs = $connsdm->Execute($sql);
	
	// jenjang pendidikan
	// $rs_pendidikan = $connsdm->Execute("select distinct on (nip) nip, rp.idpendidikan,namajenjang from pe_rwtpendidikan rp 
								// left join lv_pendidikan lp on lp.idpendidikan=rp.idpendidikan order by nip,idpendidikan desc");
	// $arr_pendidikan = array();
	// while($row_a = $rs_pendidikan->FetchRow()){
		// $arr_pendidikan[$row_a['nip']] = $row_a['namajenjang'];
	// }
	//program/jenjang studi
	$rs_jenjang = $conn->GetRow("select program,epskodeprodi from ms_unit where kodeunit='$c_unit'");
	$a = count($rs->fields['nip']);		
	if($a>0){
		$jml1=0;$jml2=0;
		while(!$rs->EOF){
			$nip = $rs->fields['nip'];
			$cek = $connp->GetOne("select 1 from tran_riwayat_status_dosen where nip='$nip' and tahun_pelaporan='$c_periode'");	
			$record = array();	
			$record['kode_tmst_dosen'] = null;
			$record['tahun_pelaporan'] = $c_periode;
			$record['kode_perguruan_tinggi'] = "405016";
			$record['kode_program_studi'] = $rs_jenjang['epskodeprodi'];
			$record['kode_jenjang_pendidikan'] = getJenjangEPS($rs_jenjang['program']);
			$record['nodos_pt'] = $nip;
			$record['status_aktif'] = getStatusAktDosenEPS($rs->fields['idstatusaktif']);
			$record['nip'] = $nip;
			
			if($cek<>'1'){		
				$jml1++;
				$ok = $connp->AutoExecute('tran_riwayat_status_dosen',$record,'INSERT');
				$message = 'Transfer Data Sukses, sebanyak : '.$jml1;
			}
			else if($cek=='1'){
				$jml2++;
				$ok = $connp->AutoExecute('tran_riwayat_status_dosen',$record,'UPDATE','nip = "$nip"',false);
				$message = 'Update Data Sukses, sebanyak : '.$jml2;
			}
			$rs->MoveNext();
		}
		// if($ok){
			$rec = array();
			$rec['kode_program_studi'] = $c_unit;
			$rec['kode_program_studi_eps'] = $rs_jenjang['epskodeprodi'];
			$rec['periode'] = $c_periode;
			$rec['thnkurikulum'] = $c_kurikulum;
			$rec['thnpelaporan'] = $c_thnpelaporan;
			$rec['tabelpdpt'] = 'tran_riwayat_status_dosen';
			$rec['nip'] = $_SESSION['SIAKAD_USER'];
			$rec['namapetugas'] = $_SESSION['SIAKAD_NAME'];
			$rec['t_updatetime'] = date("Y-m-d H:i:s");
			$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
			$ok = $connp->AutoExecute('ms_transferpdpt',$rec,'INSERT');	
		// }
	
	}else{
		$message = 'Tidak ada data yang ditemukan';
	}
	
?>