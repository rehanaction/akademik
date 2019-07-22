<?php
	//kapasitas mhs baru
	include("eps_tbkod.php");
	// $connsdm = Factory::getConnSDM();
	
	$c_unit = $r_unit;
	$c_periode = $_POST["periode"];	
	$c_thnpelaporan = $_POST["tahun"];	
	$c_kurikulum = $_POST["thnkurikulum"];	
	
	#diambil per prodi dan periode ttt
	$sql = "select count(*) from ms_pendaftar where periode='$c_periode'";
	if($c_unit<>'000000')
		$sql .= " and (kodeunit='$c_unit' or kodeunit2='$c_unit')";
	$rs_jml_calon = $conn->GetOne($sql);
	
	#target mhs baru
	$rs_target = $conn->GetRow("select * from ms_unit where kodeunit='$c_unit'");
	
	#lulus seleksi
	$sql_lulus = "select count(*) from ms_pendaftar p left join ms_unit u on p.unitujitulis=u.kodeunit where 1=1 and iskesehatan = 1 and p.periode= '$c_periode' 
				and p.ispsikotes=1 and (p.unitujitulis in (select kodeunit from ms_unit where ispsikotes=1))  ";
	if($c_unit<>'000000')
		$sql_lulus .= " and (p.unitujitulis='$c_unit')";
	$rs_lulus_seleksi = $conn->GetOne($sql_lulus);
	
	#daftar ulang
	$rs_daftarulang = $conn->GetOne("select count(*) from ms_pendaftar p left join ms_unit u on u.kodeunit=p.unitujitulis 
					where p.unitujitulis='$c_unit' and periode ='$c_periode' and refnim <> '' ");
	
	//program/jenjang studi
	$rs_jenjang = $conn->GetRow("select program,epskodeprodi from ms_unit where kodeunit='$c_unit'");
	// $a = count($rs->fields['nopendaftar']);		
	// if($a>0){
		$jml1=0;$jml2=0;
		// while(!$rs->EOF){
			// $nopendaftar = $rs->fields['nopendaftar'];
			$cek = $connp->GetOne("select 1 from tran_daya_tampung where nopendaftar='$nopendaftar' and tahun_pelaporan='$c_periode'");	
			
			$record = array();	
			// $record['kode_tmst_dosen'] = null;
			$record['tahun_pelaporan'] = $c_periode;
			$record['kode_perguruan_tinggi'] = "405016";
			$record['kode_program_studi'] = $rs_jenjang['epskodeprodi'];
			$record['kode_jenjang_pendidikan'] = getJenjangEPS($rs_jenjang['program']);
			
			$record['target_mhs_baru'] 		= null;//$rs_target['target'];//harusnya target
			$record['calon_ikut_seleksi'] 	= $rs_jml_calon;
			$record['calon_lulus_seleksi'] 	= $rs_lulus_seleksi;
			$record['mendaftar_sebagai_mahasiswa'] 	= $rs_daftarulang;//daftar ulang
			$record['peserta_mengundurkan_diri'] 	= null;
			
			$record['jml_mhs_pindahan'] 	= null;
			$record['tgl_awal_kuliah_ganjil'] = null;
			$record['tgl_akhir_kuliah_ganjil'] = null;
			$record['jml_minggu_kuliah_ganjil'] = null;
			$record['tgl_awal_kuliah_genap'] = null;
			$record['tgl_akhir_kuliah_genap'] = null;
			$record['jml_minggu_kuliah_genap'] = null;
			$record['metode_kuliah'] = null;
			$record['kelas_ekstensi'] = null;
			$record['metode_kuliah_ekstensi'] = null;
			$record['kegiatan_sp'] = null;
			$record['jml_sp_setahun'] = null;
			$record['metode_sp'] = null;
			// $record['nopendaftar'] = $nopendaftar;
			
			if($cek<>'1'){		
				$jml1++;
				$ok = $connp->AutoExecute('tran_daya_tampung',$record,'INSERT');
				$message = 'Transfer Data Sukses, sebanyak : '.$jml1;
			}
			else if($cek=='1'){
				$jml2++;
				$ok = $connp->AutoExecute('tran_daya_tampung',$record,'UPDATE','nopendaftar = "$nopendaftar"',false);
				$message = 'Update Data Sukses, sebanyak : '.$jml2;
			}
			// $rs->MoveNext();
		// }
		// if($ok){
			$rec = array();
			$rec['kode_program_studi'] = $c_unit;
			$rec['kode_program_studi_eps'] = $rs_jenjang['epskodeprodi'];
			$rec['periode'] = $c_periode;
			$rec['thnkurikulum'] = $c_kurikulum;
			$rec['thnpelaporan'] = $c_thnpelaporan;
			$rec['tabelpdpt'] = 'tran_daya_tampung';
			$rec['nip'] = $_SESSION['SIAKAD_USER'];
			$rec['namapetugas'] = $_SESSION['SIAKAD_NAME'];
			$rec['t_updatetime'] = date("Y-m-d H:i:s");
			$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
			$ok = $connp->AutoExecute('ms_transferpdpt',$rec,'INSERT');	
		// }
	
	// }else{
		// $message = 'Tidak ada data yang ditemukan';
	// }
	
?>