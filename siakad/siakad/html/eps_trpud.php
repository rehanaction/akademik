<?php
	//dosen cuti/studi lanjut atau meninggal
	include("eps_tbkod.php");
	$connsdm = Factory::getConnSDM();
	
	$c_unit = $r_unit;
	$c_periode = $_POST["periode"];	
	$c_thnpelaporan = $_POST["tahun"];	
	$c_kurikulum = $_POST["thnkurikulum"];	
	
	#diambil per tahun, karena di sim adanya tahun bukan semester
	$sql = "select p.*, a.* from pe_publikasi p 
			left join ms_pegawai a on a.nip=p.nip
			where a.idjenispegawai='ID0' ";
	if($c_unit<>'000000')
		$sql .= " and a.idsatker='$c_unit'";
	$rs = $connsdm->Execute($sql);
	
	//program/jenjang studi
	$rs_jenjang = $conn->GetRow("select program,epskodeprodi from ms_unit where kodeunit='$c_unit'");
	$a = count($rs->fields['nip']);		
	if($a>0){
		$jml1=0;$jml2=0;
		while(!$rs->EOF){
			$nip = $rs->fields['nip'];
			$cek = $connp->GetOne("select 1 from tran_publikasi_dosen_tetap where nip='$nip' and tahun_pelaporan='$c_periode'");	
			// $no_urut = $connp->GetOne("select coalesce(no_penelitian,0) from tran_publikasi_dosen_tetap where nip='$nip'");	
			
			$record = array();	
			$record['kode_tmst_dosen'] = null;
			$record['tahun_pelaporan'] = $c_periode;
			$record['kode_perguruan_tinggi'] = "405016";
			$record['kode_program_studi'] = $rs_jenjang['epskodeprodi'];
			$record['kode_jenjang_pendidikan'] = getJenjangEPS($rs_jenjang['program']);
			
			$record['nidn'] = $rs->fields['nidn'];
			$record['no_penelitian'] = null;//no.urut
			$record['jenis_penelitian'] = getJenisPenelitian($rs->fields['jenispublikasi']);//A=penelitian, B=non-penelitian
			$record['media_publikasi'] = getMediaPublikasi($rs->fields['jenispublikasi']);
			$record['kode_pengarang'] = 'B';
			$record['penelitian_dilaksanakan'] = $rs->fields['isvalid'];
			$record['periode_penelitian'] = $c_periode;
			$record['jenis_pembiayaan'] = null;
			if($rs->fields['mandiriteam'] == 't')
				$mandiritim = 'K';
			else
				$mandiritim = 'M';
			$record['mandiritim'] = $mandiritim;
			
			#cek banyak judul tiap dosen
			$rs_judul = $connsdm->Execute("select * from pe_publikasi where nip='$nip'");
			$i=1;while($rowa = $rs_judul->FetchRow()){
				if($i<=5){
					$record['judul_penelitian'.$i] = $rowa['judulpublikasi'];
				}
				$i++;
			}
			$record['nip'] = $nip;
			
			if($cek<>'1'){		
				$jml1++;
				$ok = $connp->AutoExecute('tran_publikasi_dosen_tetap',$record,'INSERT');
				$message = 'Transfer Data Sukses, sebanyak : '.$jml1;
			}
			else if($cek=='1'){
				$jml2++;
				$ok = $connp->AutoExecute('tran_publikasi_dosen_tetap',$record,'UPDATE','nip = "$nip"',false);
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
			$rec['tabelpdpt'] = 'tran_publikasi_dosen_tetap';
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