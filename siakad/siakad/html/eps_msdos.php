<?php
	include("eps_tbkod.php");
	$connsdm = Factory::getConnSDM();
	// $connp = Factory::getConnPdpt();
	
	$c_unit = $r_unit;
	$c_periode = $_POST["periode"];	
	$c_thnpelaporan = $_POST["tahun"];	
	$c_kurikulum = $_POST["thnkurikulum"];	
	
	$sql = "select p.*  from ms_pegawai p 
			left join lv_statusaktif ls on ls.idstatusaktif=p.idstatusaktif 
			left join lv_statuspeg sp on sp.idstatuspegawai=p.idstatuspegawai
			where idjenispegawai='ID0' ";
	if($c_unit<>'000000')
		$sql .= "and p.idsatker='$c_unit'";
	$rs = $connsdm->Execute($sql);
	// $connsdm->debug=true;
	// $conn->debug=true;
	// jenjang pendidikan
	$rs_pendidikan = $connsdm->Execute("select distinct on (nip) nip, rp.idpendidikan,namajenjang from pe_rwtpendidikan rp 
								left join lv_pendidikan lp on lp.idpendidikan=rp.idpendidikan order by nip,idpendidikan desc");
	$arr_pendidikan = array();
	while($row_a = $rs_pendidikan->FetchRow()){
		$arr_pendidikan[$row_a['nip']] = $row_a['namajenjang'];
	}
	//program/jenjang studi
	$rs_jenjang = $conn->GetRow("select program,epskodeprodi from ms_unit where kodeunit='$c_unit'");
	
	$a = count($rs->fields['nip']);		
	if($a>0){
		$jml1=0;$jml2=0;
		while(!$rs->EOF){	
			$nip = $rs->fields['nip'];
			$cek = $connp->GetOne("select 1 from tmst_dosen where nip='$nip'");	
			$record = array();	
			$record['nip'] = $nip;
			$record['kode_perguruan_tinggi'] = "405016";
			$record['kode_jenjang_pendidikan'] = getJenjangEPS($rs_jenjang['program']);
			$record['kode_program_studi'] = trim(CStrNull($rs_jenjang['epskodeprodi']));//kode program studi ini yg di sim atau di dikti?
			$record['nomor_ktp'] = trim(CStrNull($rs->fields['noktp']));
			$record['nidn'] = trim(CStrNull($rs->fields['nidn']));
			$record['nama_dosen'] = trim(CStrNull($rs->fields['nama']));
			$record['gelar_akademik'] = $rs->fields['gelardepan'].$rs->fields['gelarbelakang'];
			$record['tempat_lahir'] = $rs->fields['tmplahir'];
			$record['tanggal_lahir'] = $rs->fields['tgllahir'];
			$record['jenis_kelamin'] = $rs->fields['jeniskelamin'];
			
			$record['kode_jabatan_akademik'] = getJabatanEPS($rs->fields['idjfungsional']);
			$record['kode_pendidikan_tertinggi'] = getPendidikanTertinggiEPS($arr_pendidikan[$nip]);
			$record['ikatan_kerja'] = getStatusIkatanDosen($rs->fields['idstatuspegawai']);
			$record['status_aktif'] = getStatusAktDosenEPS($rs->fields['idstatusaktif']);
			$record['mulai_semester'] = '';
			$record['nip_pns'] = $rs->fields['nippns'];
			$record['kode_instansi'] = '';
			$record['akta'] = '';
			$record['telp_rumah'] = $rs->fields['telepon'];
			$record['hp'] = $rs->fields['nohp'];
			$record['email'] = $rs->fields['email'];
			
			// log transfer
			// $rec = array();
			
			if($cek<>'1'){		
				$jml1++;
				$ok = $connp->AutoExecute('tmst_dosen',$record,'INSERT');
				$message = 'Transfer Data Sukses, sebanyak : '.$jml1;
			}
			else if($cek=='1'){
				$jml2++;
				$ok = $connp->AutoExecute('tmst_dosen',$record,'UPDATE','nip = "$nip"',false);
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
			$rec['tabelpdpt'] = 'tmst_dosen';
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