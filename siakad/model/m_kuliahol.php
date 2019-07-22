<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKuliah extends mModel {
		const schema = 'akademik';
		const table = 'ak_kuliah';
		const order = 'perkuliahanke, tglkuliah';
		const key = 'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok';
		const label = 'jurnal perkuliahan';
		const uptype = 'jurnal';
		
		
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'kelas':
					require_once(Route::getModelPath('kelaspraktikum'));
					
					return mKelasPraktikum::getCondition($key, 'thnkurikulum , kodemk , kodeunit , periode , kelasmk,jeniskuliah, kelompok');
				case 'periode': return "k.periode = '$key'";
				case 'tglkuliah': return "k.tglkuliah = '$key'";
				case 'sistemkuliah': return "kls.sistemkuliah = '$key'";
				case 'kodemk': return "k.kodemk = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
		function dataQuery($key) {
			$sql = "select * from ".static::table()." where ".static::getCondition($key);
			
			return $sql;
		}
		
		// mendapatkan daftar per kelas
		function getListPerKelas($conn,$key,$selesai=false) {
			$sql = "select perkuliahanke,isonline,tglkuliah,to_char(tglkuliah,'YYYYMMDD') as ftglkuliah,kelompok,jeniskuliah from ".static::table()."
					where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok');
			if($selesai)
				$sql .= " and statusperkuliahan = 'S' and isonline=-1 ";
			
			$sql .= " order by ".static::order;
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan jumlah jurnal
		function getJumlahPerKelas($conn,$key,$topik=false) {
			$sql = "select count(*) from ".static::table()." where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk');
			if($topik)
				$sql .= " and topikkuliah is not null and keterangan is not null";
			
			return (int)$conn->GetOne($sql);
		}
		
		// jenis kuliah
		function jenisKuliah($conn) {
			$sql="select idjeniskuliah,namajeniskuliah from akademik.lv_jeniskuliah order by namajeniskuliah";
			
			return Query::arrQuery($conn,$sql);
		}
		function getdata_jurnal($conn,$r_key){
		$sql="select perkuliahanke, perkuliahanke||' - '||CASE WHEN statusperkuliahan='S' THEN 'Selesai' WHEN statusperkuliahan='J' THEN 'Terjadwal' WHEN statusperkuliahan='B' THEN 'BATAL' END as namaperkuliahan
			from ".static::table()." where ". static::getCondition($r_key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk')." order by perkuliahanke";
		
			
		$rs=$conn->Execute($sql);
		
			$a_data=array();
			while ($row = $rs->FetchRow()){
				$a_data[$row['perkuliahanke']]=$row['namaperkuliahan'];
			}
			return $a_data;

		}
		
		function getListSelesai($conn,$r_key) {
			$sql = "select perkuliahanke, tglkuliahrealisasi, koderuangrealisasi, waktumulairealisasi, waktuselesairealisasi
					from ".static::table()."
					where ". static::getCondition($r_key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok')."
					and statusperkuliahan = 'S'
					order by perkuliahanke";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_label = $row['perkuliahanke'];
				$t_label .= ' - '.CStr::formatDateTimeInd($row['tglkuliahrealisasi'],false,true);
				$t_label .= ' - '.$row['koderuangrealisasi'];
				$t_label .= ' - '.CStr::formatJam($row['waktumulairealisasi']).' s.d. '.CStr::formatJam($row['waktuselesairealisasi']);
				
				$a_data[$row['perkuliahanke']] = $t_label;
			}
			
			return $a_data;
		}
		
		function isPesertakelas($conn, $r_key, $nim=null){
			if(empty($nim))
				list($thnkurikulum, $kodemk, $kodeunit, $periode, $kelasmk, $perkuliahanke, $nim)=explode('|', $r_key);
			else
				list($thnkurikulum, $kodemk, $kodeunit, $periode, $kelasmk)=explode('|', $r_key);
			
			$key=$thnkurikulum.'|'.$kodemk.'|'.$kodeunit.'|'.$periode.'|'.$kelasmk;
			$sql = "select 1 from ".static::table('ak_krs')." k
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk','k')." and m.nim='$nim' order by k.nim";
			return $conn->GetOne($sql);
		}
		
		// status kuliah
		function statusKuliah($jenis='') {
			if (($jenis=='P')){
				$data = array('J' => 'Terjadwal');
			}else if ($jenis=='R'){
				$data = array('S' => 'Selesai','M'=>'Makeup','B' => 'Batal');			
			}
			else{
				$data =  array( ''=>'Terjadwal', 'J'=>'Terjadwal', 'S' => 'Selesai','M'=>'Makeup','B' => 'Batal');
			}
			return $data;
		}
		function getRealisasi($conn, $r_key){
		$sql="select * from ".static::table()." where ".static::getCondition($r_key, 'thnkurikulum,kodemk,kodeunit,periode,kelasmk');
			
			$rs = $conn->Execute($sql);
			$data=array();
			while ($row = $rs->FetchRow()){
				$data[$row['perkuliahanke']][$row['kelompok']][$row['jeniskuliah']]=true;
			}
			return $data;
		
		}
		function getTglJurnal($conn,$key,$col=null){
			if(empty($col))
				$col = 'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke';
			
			$sql="select tglkuliah from ".static::table()." where ".static::getCondition($key,$col);
			return $conn->GetOne($sql);
		}
		function getListPerkuliahan($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter){
			$sql="select k.statusperkuliahan,k.tglkuliah,akademik.f_formatjam(k.waktumulai::numeric)||'-'||akademik.f_formatjam(k.waktuselesai::numeric) as waktuperencanaan,
				akademik.f_formatjam(k.waktumulairealisasi::numeric)||'-'||akademik.f_formatjam(k.waktuselesairealisasi::numeric) as wakturealisasi,
				k.kodemk,k.kelasmk,k.koderuang,kr.namamk,kr.sks,k.jeniskuliah,k.nipdosen,k.nipdosenrealisasi,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namadosenperencanaan,
				akademik.f_namalengkap(pr.gelardepan,pr.namadepan,pr.namatengah,pr.namabelakang,pr.gelarbelakang) as namadosenrealisasi,
				k.tglkuliah,k.tglkuliahrealisasi,k.waktumulai,k.waktuselesai,k.waktumulairealisasi,k.waktuselesairealisasi,k.isonline, k.isopen,
				kl.sistemkuliah,kl.jumlahpeserta,kl.dayatampung
				from akademik.ak_kuliah k
				left join akademik.ak_kelas kl using (kodemk,kodeunit,thnkurikulum,periode,kelasmk)
				join akademik.ak_kurikulum kr using (kodemk,kodeunit,thnkurikulum)
				left join sdm.ms_pegawai p on k.nipdosen::text = p.idpegawai::text
				left join sdm.ms_pegawai pr on k.nipdosenrealisasi::text = pr.idpegawai::text
				join gate.ms_unit u using(kodeunit)";
			/*
			$sqlu="select k.tglujian as tglkuliah,akademik.f_formatjam(k.waktumulai::numeric)||'-'||akademik.f_formatjam(k.waktuselesai::numeric) as waktuperencanaan,
				k.kodemk,k.kelasmk,k.koderuang,kr.namamk,kr.sks,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namadosenperencanaan,
				akademik.f_namalengkap(pr.gelardepan,pr.namadepan,pr.namatengah,pr.namabelakang,pr.gelarbelakang) as namadosenrealisasi
				from akademik.ak_jadwalujian k
				join akademik.ak_kurikulum kr using (kodemk,kodeunit,thnkurikulum)
				left join sdm.ms_pegawai p on k.nippengawas1::text = p.idpegawai::text
				left join sdm.ms_pegawai pr on k.nippengawas2::text = pr.idpegawai::text
				join gate.ms_unit u using(kodeunit)
				where 1=1";
				
				foreach($a_filter as $filter){
					$filter=str_replace('k.tglkuliah','k.tglujian',$filter);
					$sqlu.=" and $filter";
				}
				
			$dataujian=$conn->GetArray($sqlu);
			
			foreach($dataujian as $rowu){
				$rowu+=array('jenis'=>'Ujian');
				$a_data[]=$rowu;
			}*/
			return static::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
		}
		function getReportListPerkuliahan($conn,$kolom,$sort,$filter){
			return self::getListPerkuliahan($conn,$kolom,-1,1,$sort,$filter);
		}
		function cekIp($conn,$key){
			$koderuang=$conn->GetOne("select koderuangrealisasi from ".static::table()." where ".static::getCondition($key));
			$ip=$conn->GetOne("select ipruang from ".static::table('ms_ruang')." where koderuang='$koderuang'");
			//echo $ip.'-'.$_SERVER['REMOTE_ADDR'];
			if($ip==$_SERVER['REMOTE_ADDR'])
				return array(false,'');
			else
				return array(true,'Maaf, Bapak/Ibu tidak dijadwalkan menggunakan Ruang Ini. Ruang yang seharusnya adalah '.$koderuang);
		}
		function cekTgl($conn,$key){
			
			$a_data=$conn->GetRow("select tglkuliahrealisasi,waktumulairealisasi,waktuselesairealisasi from ".static::table()." where ".static::getCondition($key));
			if($a_data['tglkuliahrealisasi']!=date('Y-m-d'))
				return array(true,'Mohon Maaf, Pertemuan ini dijadwalkan tanggal '.CStr::formatDateInd($a_data['tglkuliahrealisasi']));
			else
				return array(false,'');
		}
		function cekWaktu($conn,$key){
			
			$a_data=$conn->GetRow("select tglkuliahrealisasi,waktumulairealisasi,waktuselesairealisasi from ".static::table()." where ".static::getCondition($key));
			if(!(date('Hi')>=$a_data['waktumulairealisasi'] and date('Hi')<=$a_data['waktuselesairealisasi']))
				return array(true,'Mohon Maaf, Pertemuan ini dijadwalkan pukul '.CStr::formatJam($a_data['waktumulairealisasi']).' - '.CStr::formatJam($a_data['waktuselesairealisasi']).'. Sekarang pukul '.date('H:i'));
			else
				return array(false,'');
		}
		//monitoring ruang & pengajaran per tanggal
		function getMonitoringRuang($conn,$kodeunit, $periode,$tgl,$waktumin,$waktumax) {
			//array jenis
			$jeniskul=array('K'=>'Kuliah','P'=>'Praktikum','R'=>'Tutorial');
			$status=array('0'=>'Tatapp Muka','-1'=>'Online');
      	
			// mendapatkan ruang
			$sql = "select koderuang, dayatampung from ".static::table('ms_ruang')."  where isaktif=-1 order by koderuang ";
			$rs = $conn->Execute($sql);
			
			//info left dan right
			$sql_unit="select infoleft, inforight from gate.ms_unit where kodeunit='$kodeunit'";
			$unit=$conn->Execute($sql_unit);			
			$u_row = $unit->FetchRow();
			
			$a_jadwal = array();
      $a_tampung = array();
			while($row = $rs->FetchRow())
				$a_jadwal[$row['koderuang']] = array();
				$a_jadwal[$row['dayatampung']] = array();
				
			
			// mendapatkan jadwal
			$sql = "select p.namadepan,p.gelardepan,p.namatengah,p.namabelakang,p.gelarbelakang,mu.namaunit,k.kodemk,
          m.namamk,k.kelasmk,k.tglkuliahrealisasi, r.dayatampung,
					k.koderuangrealisasi,k.waktumulairealisasi,k.waktuselesairealisasi,k.jeniskuliah,k.isonline
					from ".static::table()." k 
          join ".static::table('ak_matakuliah')." m using (thnkurikulum,kodemk)
					join akademik.ak_kelas kls using (thnkurikulum,kodemk,kelasmk,periode,kodeunit)
					left join akademik.ak_mengajar am on am.thnkurikulum=kls.thnkurikulum and am.kodemk=kls.kodemk and am.kelasmk=kls.kelasmk and am.periode=kls.periode and am.kodeunit=kls.kodeunit
					left join sdm.ms_pegawai p on p.idpegawai::text=am.nipdosen
					left join gate.ms_unit mu on mu.kodeunit = k.kodeunit
					left join gate.ms_unit u on u.kodeunit = k.kodeunit
          left join akademik.ms_ruang r on r.koderuang = k.koderuangrealisasi
					where k.periode = '$periode' and k.tglkuliahrealisasi='$tgl' AND 
						  u.infoleft>='".$u_row['infoleft']."' AND u.inforight<='".$u_row['inforight']."'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				
					
					if($row['tglkuliahrealisasi'] == $tgl) {
						$t_jadwal = array();
            $t_jadwal['dayatampung'] = trim($row['dayatampung']);
						$t_jadwal['waktumulairealisasi'] = trim($row['waktumulairealisasi']);
						$t_jadwal['waktuselesairealisasi'] = trim($row['waktuselesairealisasi']);
						$t_jadwal['kodemk'] = $row['kodemk'];
						$t_jadwal['namamk'] = $row['namamk'];
						$t_jadwal['kelasmk'] = $row['kelasmk'];
						$t_jadwal['namaunit'] = $row['namaunit'];
						$t_jadwal['jeniskul'] = $jeniskul[$row['jeniskuliah']];
						$t_jadwal['status'] = $status[$row['isonline']];
						$t_jadwal['isonline'] = $row['isonline'];
						$t_jadwal['namadosen'] = $row['gelardepan'].' '.$row['namadepan'].' '.$row['namatengah'].' '.$row['namabelakang'].', '.$row['gelarbelakang'];
						
						// hanya yang lengkap
						if(empty($t_jadwal['waktumulairealisasi']) or empty($t_jadwal['waktuselesairealisasi']))
							continue;
						
						$a_jadwal[$row['koderuangrealisasi']][$t_jadwal['waktumulairealisasi']][] = $t_jadwal;
					}
				
			}
			
			//masuykkan data untuk monitoring ujian
			$sqlujian="select uj.*,k.namamk,u.namaunit from ".static::table('ak_jadwalujian')." uj
						join ".static::table('ak_kurikulum')." k using (thnkurikulum,kodeunit,kodemk)
						join gate.ms_unit u using (kodeunit)
						where uj.periode = '$periode' and uj.tglujian='$tgl' AND 
						  u.infoleft>='".$u_row['infoleft']."' AND u.inforight<='".$u_row['inforight']."'";
			$rsujian = $conn->Execute($sqlujian);
			while($rowujian = $rsujian->FetchRow()) {
			
					
					if($rowujian['tglujian'] == $tgl) {
						$t_jadwal = array();
						$t_jadwal['waktumulairealisasi'] = trim($rowujian['waktumulai']);
						$t_jadwal['waktuselesairealisasi'] = trim($rowujian['waktuselesai']);
						$t_jadwal['kodemk'] = $rowujian['kodemk'];
						$t_jadwal['namamk'] = $rowujian['namamk'];
						$t_jadwal['kelasmk'] = $rowujian['kelasmk'];
						$t_jadwal['namaunit'] = $rowujian['namaunit'];
						$t_jadwal['jeniskul'] = 'Ujian';
						$t_jadwal['status'] = 'Tatap Muka';
						$t_jadwal['isonline'] = -2;
						
						
						// hanya yang lengkap
						if(empty($t_jadwal['waktumulairealisasi']) or empty($t_jadwal['waktuselesairealisasi']))
							continue;
						
						$a_jadwal[$rowujian['koderuang']][$t_jadwal['waktumulairealisasi']][] = $t_jadwal;
					}
				
			}
			
			// diurutkan dulu
			foreach($a_jadwal as $t_ruang => $t_jadwal)
				ksort($a_jadwal[$t_ruang]);
			
			$a_bentrok = array();
			foreach($a_jadwal as $t_ruang => $t_mjadwal) {
				$t_lmulai = $waktumin;
				
				if(!empty($t_mjadwal)) {
					foreach($t_mjadwal as $t_ajadwal) {
						foreach($t_ajadwal as $t_jadwal) {
							$t_mulai = $t_jadwal['waktumulairealisasi'];
							$t_selesai = $t_jadwal['waktuselesairealisasi'];
							
							if($t_mulai > $t_selesai) {
								$t_temp = $t_mulai;
								$t_mulai = $t_selesai;
								$t_selesai = $t_temp;
							}
							
							// tertumpuk jadwal lain
							if($t_selesai <= $t_lmulai and !empty($t_truang['id'])) {
								$a_bentrok[$t_truang['id']] = true;
								continue;
							}
							
							// buat jadwal kosong
							if($t_mulai > $t_lmulai) {
								$t_truang = array();
								$t_truang['mulai'] = $t_lmulai;
								$t_truang['selesai'] = $t_mulai;
								$t_truang['lebar'] = Date::lamaMenit($t_lmulai,$t_mulai);
								$t_truang['status'] = false;
								
								$a_truang[$t_ruang][] = $t_truang;
								
								$t_lmulai = $t_mulai;
							}
							else if($t_mulai < $t_lmulai) {
								if($t_selesai < $t_lmulai)
									continue;
								else
									$t_mulai = $t_lmulai;
							}
							
							if($t_selesai > $waktumax)
								$t_selesai = $waktumax;
							
							$t_truang = array();
							$t_truang['mulai'] = $t_mulai;
							$t_truang['selesai'] = $t_selesai;
							$t_truang['lebar'] = Date::lamaMenit($t_mulai,$t_selesai);
							$t_truang['status'] = true;
							$t_truang['keterangan'] = $t_jadwal['jeniskul'].'<br>'.$t_jadwal['kodemk'].' - '.$t_jadwal['namamk'].'<br>Kelas '.$t_jadwal['kelasmk'].': '.CStr::formatJam($t_mulai).' - '.CStr::formatJam($t_selesai).'<br>Prodi: '.$t_jadwal['namaunit'].'<br> Status: '.$t_jadwal['status'].'<br>Dosen: '.$t_jadwal['namadosen'];
							$t_truang['kodemk'] = $t_jadwal['kodemk'].' ('.$t_jadwal['kelasmk'].')';
							$t_truang['isonline'] = $t_jadwal['isonline'];
							
							$a_truang[$t_ruang][] = $t_truang;
							
							$t_lmulai = $t_selesai;
						}
					}
				}
				
				// bila ada sisa
				if($t_lmulai < $waktumax) {
					$t_truang = array();
					$t_truang['mulai'] = $t_lmulai;
					$t_truang['selesai'] = $waktumax;
					$t_truang['lebar'] = Date::lamaMenit($t_lmulai,$waktumax);
					$t_truang['status'] = false;
					
					$a_truang[$t_ruang][] = $t_truang;
					
					$t_lmulai = $waktumax;
				}
			}
			
			return $a_truang;
		}
    
		function getKuliahOnline($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter){
			$sql="select u.namaunit,mk.kodemk,mk.namamk,mk.semmk,kls.kelasmk,k.perkuliahanke,k.kelompok,k.tglkuliahrealisasi,k.waktumulairealisasi,k.waktuselesairealisasi,
				k.jeniskuliah,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen,k.nipdosen,k.nipdosenrealisasi
				from ".static::table()." k
				join ".static::table('ak_kurikulum')." mk using (thnkurikulum , kodeunit , kodemk )
				join ".static::table('ak_kelas')." kls using (periode , thnkurikulum , kodeunit , kodemk , kelasmk)
				join gate.ms_unit u on u.kodeunit=k.kodeunit
				left join sdm.ms_pegawai p on p.idpegawai::text=k.nipdosenrealisasi";
			
			return static::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
		}
		function genPertemuan($conn,$kodeunit,$periode,$sistemkuliah,$r_status){
			require_once(Route::getModelPath('setonline'));
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			
			//ambil array aturan
			$aturan=mSetOnline::getArray($conn,$periode,$kodeunit);
			
			//ambil data 
			$sql="select k.thnkurikulum , k.kodemk , k.kodeunit , k.periode , k.kelasmk , k.perkuliahanke, k.tglkuliah, k.jeniskuliah, 
				k.kelompok,mk.semmk from ".static::table()." k
				join ".static::table('ak_kurikulum')." mk using (thnkurikulum , kodeunit , kodemk )
				join ".static::table('ak_kelas')." kls using (periode , thnkurikulum , kodeunit , kodemk , kelasmk)
				join gate.ms_unit u on u.kodeunit=k.kodeunit
				where k.jeniskuliah='K' and k.statusperkuliahan!='S' and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight']."
				and k.periode='$periode'";
			if(!empty($sistemkuliah))
				$sql.=" and kls.sistemkuliah='$sistemkuliah'";
			$datajurnal=$conn->GetArray($sql);
			$conn->BeginTrans();
			$ok=true;
			$jum=0;
			foreach($datajurnal as $row){
				$keyonline=$row['kodeunit'].'|'.$row['periode'].'|'.$row['semmk'].'|'.$row['perkuliahanke'];
				if(isset($aturan[$keyonline])){
					$key=$row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['periode'].'|'.$row['kelasmk'].'|'.$row['perkuliahanke'].'|'.$row['tglkuliah'].'|'.$row['jeniskuliah'].'|'.$row['kelompok'];
					$record=array();
					$record['isonline']=$r_status=='seton'?'-1':'0';
					$err = Query::recUpdate($conn,$record,static::table(),static::getCondition($key));
					$jum++;
					if($err)
						break;
				}
			}
			
			//set status
			if(!$err){
				$ok=true;
				$msg=array(false,'Set kelas online berhasil sebanyak '.$jum);
			}else{
				$ok=false;
				$msg=array(true,($r_status=='seton'?'Set':'Pembatalan').' kelas online gagal');
			}
			$conn->CommitTrans($ok);
			
			return $msg;	
		}
		function unsetHonor($conn,$key){
			require_once(Route::getModelPath('honordosen'));
			$cekvalid=$conn->GetRow("select 1 from akademik.ak_honordosen where validhonor=-1 and ".mHonorDOsen::getCondition($key));
			if(empty($cekvalid))
				$kelas=$conn->Execute("update akademik.ak_kuliah set validhonorkuliah=null where ".static::getCondition($key));
		}
		
		function cekRangeTime($startdate,$starttime,$enddate,$endtime){
			$starttime=Cstr::formatJam($starttime);
			$endtime=Cstr::formatJam($endtime);
			
			$start=Date::toTimestamp($startdate	,$starttime);
			$end=Date::toTimestamp($enddate,$endtime);
			$now=time();
			
			if($now>=$start and $now<=$end)
				return true;
			else
				return false;
			
		}
		
		function pindahTanggalKuliah($conn,$status,$record,$periode, $tglkuliah, $kodemk){
			$kondisi=" periode='$periode' and tglkuliah='$tglkuliah' and statusperkuliahan<>'S'";
			if($kodemk!='')
				$kondisi.=" and kodemk='$kodemk'";
				
			$err = Query::recUpdate($conn,$record,static::table(),$kondisi,false);
		
			if($status){
				return static::updateStatus($conn);
			}else{
				return $err;
			}
		}
	}
?>
