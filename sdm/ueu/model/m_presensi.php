<?php
	// model presensi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPresensi extends mModel {
		const schema = 'sdm';
		
		//mendapatkan data rekap presensi
		function listQueryRekapPresensi($r_key) {
			$sql = "select r.*
					from ".self::table('pe_presensi')." r 
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//input presensi
		function listQueryInputPresensi($r_key) {
			$sql = "select r.*,a.absensi
					from ".self::table('pe_inputpresensi')." r 
					left join ".self::table('ms_absensi')." a on a.kodeabsensi=r.kodeabsensi
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataInputPresensi($r_subkey) {
			$sql = "select * from ".self::table('pe_inputpresensi')."
					where nourutpresensi = '$r_subkey'";
			
			return $sql;
		}
		
		//hapus dulu presensi detail
		function deletePresensiDetail($conn,$r_subkey){
			$sql = "select * from ".self::table('pe_inputpresensi')." where nourutpresensi = '$r_subkey'";
			$row = $conn->GetRow($sql);
			
			$tglmulai = $row['tglmulai'];
			$tglselesai = !empty($row['tglselesai']) ? $row['tglselesai'] : $row['tglmulai'];
			
			$err = Query::qDelete($conn,self::table('pe_presensidet'),"idpegawai = ".$row['idpegawai']." and tglpresensi between '$tglmulai' and '$tglselesai' and ismanual = 'Y'");
			
			return self::saveStatus($conn);
		}
		
		//simpan presensi detail
		function savePresensiDetail($conn,$r_subkey){
			$sql = "select * from ".self::table('pe_inputpresensi')." where nourutpresensi = '$r_subkey'";
			$row = $conn->GetRow($sql);
			
			$tglmulai = $row['tglmulai'];
			$tglselesai = !empty($row['tglselesai']) ? $row['tglselesai'] : $row['tglmulai'];
			
			$mulai=strtotime($tglmulai);
			$selesai=strtotime($tglselesai);
			
			$record = array();
			$record['idpegawai'] = $row['idpegawai'];
			$record['kodeabsensi'] = $row['kodeabsensi'];
			$record['tglpemasukan'] = $row['tglpemasukan'];
			$record['jamdatang'] = $row['jamdatang'];
			$record['jampulang'] = $row['jampulang'];
			$record['keterangan'] = $row['keterangan'];
			$record['ismanual'] = 'Y';
			while($mulai<=$selesai){
				$record['tglpresensi'] = date("Y-m-d",$mulai);				
				$err = Query::recInsert($conn,$record,self::table('pe_presensidet'));
				
				if($err)
					break;
					
				$mulai+=86400;
			}
				
			return self::saveStatus($conn);
		}
		
		//rekap presensi
		function listRekapPresensi(){
			$sql = "select r.*,m.nik, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap
					from ".self::table('pe_presensi')." r
					left join ".static::table('ms_pegawai')." m on m.idpegawai=r.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit";
			
			return $sql;
		}
		
		function listQueryMonitor(){
			$sql = "select p.*,m.nik, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap,t.idtipepeg,
					case when sdm.f_diffmenit(jamdatang, jampulang)::integer < 60 then 0 else sdm.f_diffmenit(jamdatang, jampulang)::integer end as menit
					from ".static::table('pe_presensidet')." p
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=m.idtipepeg";
			
			return $sql;
		}
		
		function listQueryShiftBulan(){
			$sql = "select * from ".static::table('pe_rwtshift');
			
			return $sql;
		}
		
		function jenisAbsensi($conn){
			$sql = "select kodeabsensi, absensi from ".static::table('ms_absensi')." where kodeabsensi in ('I','S','H','D') order by kodeabsensi desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function isPejabat($conn, $r_pegawai){
			$sql = "select 1 from ".static::table('ms_pegawai')." where idpegawai=$r_pegawai and idjstruktural is not null";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan tahun rekap presensi pegawai ybs
		function getTahunPresensi($conn,$r_key){
			$sql = "select substring(periode,1,4) as tahun from ".self::table('pe_presensi')." 
					where idpegawai = $r_key
					group by substring(periode,1,4)
					order by substring(periode,1,4) desc";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()){
				$data[$row['tahun']] = $row['tahun'];
			}
			
			return $data;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			if($col == 'unit') {
				global $conn, $conf;
				require_once($conf['gate_dir'].'model/m_unit.php');
				
				$row = mUnit::getData($conn,$key);
				
				return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
			if($col == 'bulan')
				return "substring(((k.tgllembur::date)::character varying),6,2) = '$key'";
			if($col == 'tahun')
				return "substring(((k.tgllembur::date)::character varying),1,4) = '$key'";
			if($col == 'periode')
				return "substring(r.periode ,1,4) = '$key'";
			if($col == 'tglpresensi')
				return "cast(tglpresensi as date) = '".CStr::formatDate($key)."'";			
			if($col == 'tipepeg')
				return "m.idtipepeg ='$key'";
			if($col == 'blnpresensi')
				return "substring(r.periode ,5,2) = '$key'";
		}
				
		//pop detail presensi
		function getPresensi($conn,$r_key,$r_periode){			
			$sql = "select d.*, a.absensi,a.color
					from ".self::table('pe_presensidet')." d
					left join ".self::table('ms_absensi')." a on a.kodeabsensi=d.kodeabsensi
					where idpegawai='$r_key' and (substring(cast(tglpresensi as varchar),1,4)||substring(cast(tglpresensi as varchar),6,2)) = '$r_periode'
					order by d.tglpresensi";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data[$row['tglpresensi'].$row['idpegawai']] = $row;
			}
			
			return $t_data;
		}
		
		//hari utk detail
		function hariAbsensi(){
			return array('0'=>'Minggu','1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu');
		}
		
		function aJenisCuti($conn){
			$sql = "select kodeabsensi,absensi,color from ".static::table('ms_absensi')." order by absensi";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data['kode'][$row['kodeabsensi']] = $row['kodeabsensi'];
				$a_data['absensi'][$row['kodeabsensi']] = $row['absensi'];
				$a_data['color'][$row['kodeabsensi']] = $row['color'];
			}
			return $a_data;
		}
		
		function aturanHadir($conn){
			$sql = "select tglberlaku,minterlambat from ".static::table('ms_keterlambatan')." order by tglberlaku desc limit 1";
			
			return $conn->GetRow($sql);
		}
		
		//mendapatkan hari libur
		function getHariLibur($conn,$r_periode){
			$sql = "select d.tgllibur,l.namaliburan,l.keterangan 
					from ".self::table('ms_liburdetail')." d
					left join ".self::table('ms_libur')." l on l.idliburan=d.idliburan
					where (substring(cast(d.tgllibur as varchar),1,4)||substring(cast(d.tgllibur as varchar),6,2)) = '$r_periode' and 
					extract(dow from d.tgllibur) <> 0 and extract(dow from d.tgllibur) <> 6";
					
			$rs = $conn->Execute($sql);
	
			while($row = $rs->FetchRow()){
				$a_data[$row['tgllibur']] = $row;
			}
			
			return $a_data;
		}
		
		//mendapatkan jumlah hari libur
		function getJumlahLibur($conn,$bulan,$tahun){
			$jumlahLibur = $conn->GetOne("select count(idliburan) from ".self::table('ms_liburdetail')." where
										  substring(tgllibur::text,5,2) = '$bulan' and substring(tgllibur::text,1,4) = '$tahun' and isliburbersama='Y'");
					
			return $jumlahLibur;
		}
		
		//memndapatkan proc kehadiran
		function getProcKehadiran($conn,$bulan,$tahun){
			//variabel perhitungan proc kehadiran
			$bulan = str_pad($bulan,2,"0", STR_PAD_LEFT);
			$tglawal = $tahun.'-'.$bulan.'-01';
			$sawal = strtotime($tglawal);
			$tglakhir = date('Y-m-d');
			$sakhir = strtotime($tglakhir);
			
			$sql = "select r.idpegawai,m.idjenispegawai,hadir,dinas,cuti
					from ".self::table('pe_presensi')." r
					left join ".static::table('ms_pegawai')." m on m.idpegawai=r.idpegawai
					where substring(r.periode ,5,2) = '$bulan' and substring(r.periode ,1,4) = '$tahun'
					and idtipepeg='K'";
			$rs = $conn->Execute($sql);
	
			while($row = $rs->FetchRow()){
				$a_data[] = $row;
			}
			
			$sql = "select tglberlaku,idjenispegawai,senin,selasa,rabu,kamis,jumat,sabtu,minggu from ".static::table('ms_kelkerja')." ";
			$rs = $conn->Execute($sql);
			
			while($rowp = $rs->FetchRow()){
				if(!empty($rowp['minggu']))
					$a_shift[$rowp['tglberlaku']][$rowp['idjenispegawai']][] = 0;
				if(!empty($rowp['senin']))
					$a_shift[$rowp['tglberlaku']][$rowp['idjenispegawai']][] = 1;
				if(!empty($rowp['selasa']))
					$a_shift[$rowp['tglberlaku']][$rowp['idjenispegawai']][] = 2;
				if(!empty($rowp['rabu']))
					$a_shift[$rowp['tglberlaku']][$rowp['idjenispegawai']][] = 3;
				if(!empty($rowp['kamis']))
					$a_shift[$rowp['tglberlaku']][$rowp['idjenispegawai']][] = 4;
				if(!empty($rowp['jumat']))
					$a_shift[$rowp['tglberlaku']][$rowp['idjenispegawai']][] = 5;
				if(!empty($rowp['sabtu']))
					$a_shift[$rowp['tglberlaku']][$rowp['idjenispegawai']][] = 6;
				$a_tglberlakushift[]=$rowp['tglberlaku'];
			}
			
			//get jumlah libur
			$libur = self::getJumlahLibur($conn,$bulan,$tahun);
			
			if(count($a_data)>0){
				foreach($a_data as $data){
					$tglawal = $tahun.'-'.$bulan.'-01';
					$sawal = strtotime($tglawal);
					$tglakhir = date('Y-m-d');
					$sakhir = strtotime($tglakhir);
					while($sawal <= $sakhir){
						$elemen=date("w",$sawal);
						//pengecekan shift sesuai tgl berlaku
						if(count($a_tglberlakushift)>0){
							foreach($a_tglberlakushift as $tglberlakushift){
								$sberlakushift = strtotime($tglberlakushift);
								if($sberlakushift<=$sawal)
									$tglshift = $tglberlakushift;
							}
							if(in_array($elemen, $a_shift[$tglshift][$data['idjenispegawai']]))						
								$hariefektif[$data['idpegawai']] += 1; //jumlah hari efektif -> belum dikurangi libur
						}
						$sawal+=86400;
					}
					
					$hariaktif[$data['idpegawai']] = $hariefektif[$data['idpegawai']]-$libur; //hari aktif -> hari wajib masuk setelah dikurangi libur
					
					$proc[$data['idpegawai']]['transport'] = round((($data['hadir'] + $data['dinas']) / $hariaktif[$data['idpegawai']]) * 100,2); 
					$proc[$data['idpegawai']]['kesra'] = round((($data['hadir'] + $data['dinas'] + $data['cuti']) / $hariaktif[$data['idpegawai']]) * 100,2); 
				}
			}
			
			return $proc;
		}
		
		//mendapatkan maksimal keterlambatan
		function getMaxTerlambat($conn){
			$sql = "select alpaterlambat from ".self::table('ms_keterlambatan')." order by tglberlaku desc limit 1";
			$maxterlambat = $conn->GetOne($sql);
			
			return $maxterlambat;
		}
		
		//mendapatkan cuti ke presensi
		function saveFromCuti($conn,$r_key){
			//select cuti yang sudah disetujui
			$rowv = $conn->GetRow("select c.*,m.jeniscuti
					from ".self::table('pe_rwtcuti')." c
					left join ".self::table('ms_cuti')." m on m.idjeniscuti = c.idjeniscuti
					where c.nourutcuti = '$r_key'");
			
			//hapus absensi dulu, utk membersihkan cuti di absensi
			$conn->Execute("delete from ".self::table('pe_presensidet')." where nourutcuti = '$r_key' and kodeabsensi = 'C' and jamdatang is null and jampulang is null");
			$rsdet = $conn->Execute("select tglpresensi,idpegawai,kodeabsensi,jamdatang from ".self::table('pe_presensidet')." where nourutcuti = '$r_key'");
			while($rowdet = $rsdet->FetchRow()){
				if(empty($rowdet['jamdatang']))
					$conn->Execute("update ".self::table('pe_presensidet')." set kodeabsensi = 'A' where idpegawai = ".$rowdet['idpegawai']." and tglpresensi = '".$rowdet['tglpresensi']."'");
			}
			$conn->Execute("update ".self::table('pe_presensidet')." set nourutcuti = null where nourutcuti = '$r_key'");
			
			//bila disetujui, maka insert ke presensi
			if($rowv['statususulan'] == 'S'){
				$rsd = $conn->Execute("select * from ".self::table('pe_rwtcutidet')." where nourutcuti = '$r_key' and ischange is null");
				
				while($rowd = $rsd->FetchRow()){
					$mulai=strtotime($rowd['tglmulai']);
					$selesai=strtotime($rowd['tglselesai']);
					
					//cek shift dari presensi
					$rss = $conn->Execute("select tglpresensi from ".self::table('pe_presensidet')." 
							where tglpresensi between '".$rowd['tglmulai']."' and '".$rowd['tglselesai']."' 
							and sjamdatang is not null and sjampulang is not null and idpegawai = ".$rowv['idpegawai']."");
					
					$a_shiftnum = array();
					while($rows = $rss->FetchRow()){
						$a_shiftnum[] = strtotime($rows['tglpresensi']);
					}
					
					$artgl=array();
					$i=0;		
					while($mulai<=$selesai){
						$nefektif=date("w",$mulai);
						if(($nefektif != 0 and $nefektif != 6) or in_array($mulai,$a_shiftnum))
							$artgl[$i++] = date('Y-m-d',$mulai);		
						$mulai+=86400;
					}		

					if(count($artgl)>0){
						$libur = $conn->Execute("select tgllibur from ".self::table('ms_liburdetail')." 
								where tgllibur between '".$rowd['tglmulai']."' and '".$rowd['tglselesai']."' and 
								extract(dow from tgllibur) <> 0 and extract(dow from tgllibur) <> 6");

						//Cek apakah hari cuti dalam hari liburan		
						while($rowl = $libur->FetchRow()){
							if(in_array($rowl['tgllibur'],$artgl) and !in_array(strtotime($rowl['tgllibur']),$a_shiftnum))
								unset($artgl[array_search($rowl['tgllibur'],$artgl)]);
						}
						
						//insert ke absensi	
						if(count($artgl)>0){				
							foreach($artgl as $keys=>$val){
								$recabs = array();
								$recabs['idpegawai'] = $rowv['idpegawai'];
								$recabs['nourutcuti'] = $r_key;
								$recabs['tglpresensi'] = $val;
								$recabs['kodeabsensi'] = 'C';
								$recabs['tglpemasukan'] = date('Y-m-d');
								$recabs['keterangan'] = 'Cuti '.$rowv['jeniscuti'].($rowv['keterangan'] != '' ? ' ('.$rowv['keterangan'].')' : '');
																
								$isexist = $conn->GetOne("select 1 from ".self::table('pe_presensidet')." where idpegawai = '".$rowv['idpegawai']."' and tglpresensi = '$val'");
								if(empty($isexist))
									Query::recInsert($conn,$recabs,self::table('pe_presensidet'));
								else
									Query::recUpdate($conn,$recabs,self::table('pe_presensidet'),"idpegawai = '".$rowv['idpegawai']."' and tglpresensi = '$val'");
							}
						}
					}
				}
			}
						
			return self::saveStatus($conn);
		}
		
		//jika hapus cuti, maka juga hapus presensi yang cuti saja
		function deleteFromCuti($conn,$r_key){
			$conn->Execute("delete from ".self::table('pe_presensidet')." where nourutcuti = '$r_key' and kodeabsensi = 'C' and jamdatang is null and jampulang is null");
			$rsdet = $conn->Execute("select tglpresensi,idpegawai,kodeabsensi,jamdatang from ".self::table('pe_presensidet')." where nourutcuti = '$r_key'");
			while($rowdet = $rsdet->FetchRow()){
				if(empty($rowdet['jamdatang']))
					$conn->Execute("update ".self::table('pe_presensidet')." set kodeabsensi = 'A' where idpegawai = ".$rowdet['idpegawai']." and tglpresensi = '".$rowdet['tglpresensi']."'");
			}
			$conn->Execute("update ".self::table('pe_presensidet')." set nourutcuti = null where nourutcuti = '$r_key'");
			
			return self::deleteStatus($conn);
		}
		
		function saveFormDinas($conn,$r_key){
			$sql = "select r.*, j.namajenisdinas from ".static::table('pe_rwtdinas')." r 
					left join ".static::table('ms_jenisdinas')." j on j.kodejenisdinas=r.kodejenisdinas
					where nodinas=$r_key";
			$row = $conn->GetRow($sql);
			
			//hapus absensi dulu, utk membersihkan cuti di absensi
			$conn->Execute("delete from ".self::table('pe_presensidet')." where nodinas = '$r_key' and kodeabsensi = 'D' and jamdatang is null and jampulang is null");
			$rsdet = $conn->Execute("select tglpresensi,idpegawai,kodeabsensi,jamdatang from ".self::table('pe_presensidet')." where nodinas = '$r_key'");
			while($rowdet = $rsdet->FetchRow()){
				if(empty($rowdet['jamdatang']))
					$conn->Execute("update ".self::table('pe_presensidet')." set kodeabsensi = 'A' where idpegawai = ".$rowdet['idpegawai']." and tglpresensi = '".$rowdet['tglpresensi']."'");
			}
			$conn->Execute("update ".self::table('pe_presensidet')." set nodinas = null where nodinas = '$r_key'");
			
			//bila disetujui, maka insert ke presensi
			if($row['issetujuatasan'] == 'S' and $row['issetujuwarek2'] == 'S' and $row['issetujukabagkeu'] == 'S' and $row['issetujukasdm'] == 'S'){
				$mulai=strtotime($row['tglpergi']);
				$selesai=strtotime($row['tglpulang']);

				$artgl=array();
				$i=0;		
				while($mulai<=$selesai){
					$nefektif=date("w",$mulai);
					if($nefektif != 0 and $nefektif != 6)
						$artgl[$i++] = date('Y-m-d',$mulai);		
					$mulai+=86400;
				}		

				if(count($artgl)>0){
					$libur = $conn->Execute("select tgllibur from ".self::table('ms_liburdetail')." 
							where tgllibur between '$row[tglpergi]' and '$row[tglpulang]' and 
							extract(dow from tgllibur) <> 0 and extract(dow from tgllibur) <> 6");

					//Cek apakah hari cuti dalam hari liburan		
					while($rowl = $libur->FetchRow()){
						if(in_array($rowl['tgllibur'],$artgl))
							unset($artgl[array_search($rowl['tgllibur'],$artgl)]);
					}
					
						//insert ke absensi	
					if(count($artgl)>0){				
						foreach($artgl as $keys=>$val){
							$recabs = array();
							$recabs['idpegawai'] = $row['pegditunjuk'];
							$recabs['nodinas'] = $r_key;
							$recabs['tglpresensi'] = $val;
							$recabs['kodeabsensi'] = 'D';
							$recabs['tglpemasukan'] = date('Y-m-d');
							$recabs['keterangan'] = 'Dinas '.$row['namajenisdinas'].' ('.$row['dalamrangka'].')';
																
							$isexist = $conn->GetOne("select 1 from ".self::table('pe_presensidet')." where idpegawai = '".$row['pegditunjuk']."' and tglpresensi = '$val'");
							if(empty($isexist))
								Query::recInsert($conn,$recabs,self::table('pe_presensidet'));
							else
								Query::recUpdate($conn,$recabs,self::table('pe_presensidet'),"idpegawai = '".$row['pegditunjuk']."' and tglpresensi = '$val'");
						}
					}
				}
			}
			
			return self::saveStatus($conn);
		}
		
		function deleteFormDinas($conn,$r_key){
			$conn->Execute("delete from ".self::table('pe_presensidet')." where nodinas = '$r_key' and kodeabsensi = 'D' and jamdatang is null and jampulang is null");
			$rsdet = $conn->Execute("select tglpresensi,idpegawai,kodeabsensi,jamdatang from ".self::table('pe_presensidet')." where nodinas = '$r_key'");
			while($rowdet = $rsdet->FetchRow()){
				if(empty($rowdet['jamdatang']))
					$conn->Execute("update ".self::table('pe_presensidet')." set kodeabsensi = 'A' where idpegawai = ".$rowdet['idpegawai']." and tglpresensi = '".$rowdet['tglpresensi']."'");
			}
			$conn->Execute("update ".self::table('pe_presensidet')." set nodinas = null where nodinas = '$r_key'");
			
			return self::deleteStatus($conn);
		}
		
		function saveDetailShift($conn,$idpegawai,$tglmulai,$tglselesai,$kelkerja){
			$sql = "delete from ".static::table('pe_presensidet')." where idpegawai=$idpegawai and tglpresensi < now()::date";
			$conn->Execute($sql);
			
			$tglmulai = date("Y-m-d");
			
			$a_ahari = array();
			$a_ahari = mPresensi::getDayPresensi();
			$a_hari = array();
			$a_hari = $a_ahari['aday'];
			
			$a_jamhadir = array();
			$a_jamhadir = mPresensi::getJamHadir($conn);
			
			$sql ="select extract(dow from dt) as nohari,dt as tgl,senin,selasa,rabu,kamis,jumat,sabtu,minggu
					from (select (generate_series('$tglmulai', '$tglselesai', '1 day'::interval))::date as dt) g
					left join sdm.ms_kelkerja j on j.kodekelkerja='$kelkerja'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$recabs = array();
				$recabs['idpegawai'] = $idpegawai;
				$recabs['tglpresensi'] = $row['tgl'];
				$recabs['tglpemasukan'] = date('Y-m-d');
				$recabs['sjamdatang'] = $a_jamhadir['jamdatang'][$row[$a_hari[$row['nohari']]]];
				$recabs['sjampulang'] = $a_jamhadir['jampulang'][$row[$a_hari[$row['nohari']]]];
				
				$isexist = $conn->GetOne("select 1 from ".self::table('pe_presensidet')." where idpegawai = $idpegawai and tglpresensi = '$row[tgl]'");
				if(empty($isexist)){					
					if (empty($recabs['sjamdatang']))
						$recabs['kodeabsensi'] = 'L';
					else
						$recabs['kodeabsensi'] = 'B';
					Query::recInsert($conn,$recabs,self::table('pe_presensidet'));
				}else
					Query::recUpdate($conn,$recabs,self::table('pe_presensidet'),"idpegawai = $idpegawai and tglpresensi = '$row[tgl]'");
			}
		}
		
		
		function deleteDetailShift($conn,$r_key){
			$col = $conn->GetRow("select idpegawai,tglakhir from ".static::table('pe_rwtharikerja')." where norwthk=$r_key");
			$sql = "delete from ".static::table('pe_presensidet')." 
					where idpegawai=$col[idpegawai] and tglpresensi between now()::date and '$col[tglakhir]'";
			$conn->Execute($sql);
		}
		
		function saveDetailShiftBulan($conn,$idpegawai,$r_kode){
			$sql = "delete from ".static::table('pe_presensidet')." where idpegawai=$idpegawai and tglpresensi < now()::date";
			$conn->Execute($sql);
			
			$tglmulai = strtotime(date("Y-m-d"));
			$tglselesai = strtotime(date("Y-".substr($r_kode,4,2)."-t"));
			
			if ($tglselesai >= $tglmulai){
				$sql ="select tglshift as tgl,jamdatang,jampulang from ".static::table('pe_rwtshiftdet')." where kodeshift='$r_kode'";
				$rs = $conn->Execute($sql);
				$a_data = array();
				while($row = $rs->FetchRow()){
					$a_data['sdatang'][$row['tgl']] = $row['jamdatang'];
					$a_data['spulang'][$row['tgl']] = $row['jampulang'];
				}
				while($tglmulai <= $tglselesai){
					$tgl = date("Y-m-d",$tglmulai);
					$recabs = array();
					$recabs['idpegawai'] = $idpegawai;
					$recabs['tglpresensi'] = $tgl;
					$recabs['tglpemasukan'] = date('Y-m-d');
					$recabs['sjamdatang'] = $a_data['sdatang'][$tgl];
					$recabs['sjampulang'] = $a_data['spulang'][$tgl];
					
					$isexist = $conn->GetOne("select 1 from ".self::table('pe_presensidet')." where idpegawai = $idpegawai and tglpresensi = '$tgl'");
					if(empty($isexist)){					
						if (empty($recabs['sjamdatang']))
							$recabs['kodeabsensi'] = 'L';
						else
							$recabs['kodeabsensi'] = 'B';
						Query::recInsert($conn,$recabs,self::table('pe_presensidet'));
					}else
						Query::recUpdate($conn,$recabs,self::table('pe_presensidet'),"idpegawai = $idpegawai and tglpresensi = '$tgl'");
					$tglmulai+=86400;
				}
			}
		}
		
		function deleteDetailShiftBulan($conn,$r_key,$rkey){
			$tglakhir = date("Y-".substr($r_key,4,2)."-t");
			$sql = "delete from ".static::table('pe_presensidet')." 
					where idpegawai=$rkey and tglpresensi between now()::date and '$tglakhir'";
			$conn->Execute($sql);
			
			$a_message = mPresensi::delete($conn,($r_key.'|'.$rkey),'pe_rwtshiftpeg','kodeshift,idpegawai');
			return $a_message;
		}
		
		function deleteShiftBulan($conn,$r_key){			
			/*$tglakhir = date("Y-".substr($r_key,4,2)."-t");
			$sql = "delete from ".static::table('pe_presensidet')." 
					where tglpresensi between now()::date and '$tglakhir'";
			$conn->Execute($sql);*/
			
			mPresensi::delete($conn,$r_key,'pe_rwtshiftdet','kodeshift');
			$a_message = mPresensi::delete($conn,$r_key,'pe_rwtshift','kodeshift');
			
			return $a_message;
		}
		
		// mendapatkan kueri list untuk setting kehadiran
		function listQuerySetKehadiran() {
			$sql = "select * from ".static::table('ms_keterlambatan');
			
			return $sql;
		}
		
		// mendapatkan kueri list untuk setting lembur
		function listQueryShift() {
			$sql = "select s.*,t.tipepeg||' - '||j.jenispegawai as namajenispegawai 
					from ".static::table('ms_kelkerja')." s
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai=s.idjenispegawai
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg";
			
			return $sql;
		}
		
		function getPresensiDate($conn, $date){
			$sql = "select idpegawai from ".static::table('pe_presensidet')." where tglpresensi='$date'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data['idpegawai'][$row['idpegawai']] = $row['idpegawai'];
				$a_data['norwthk'][$row['idpegawai']] = $row['norwthk'];
			}
				
			return $a_data;
		}
		
		//digunakan untuk nama kolom hari pada tabel shift
		function aCHari(){
			return array("senin","selasa","rabu","kamis","jumat","sabtu","minggu");
		}
		
		function getCJamHadir($conn){
			$sql = "select kodejamhadir, substring(jamdatang,1,2) || ':' || substring(jamdatang,3,2) || ' s/d ' || substring(jampulang,1,2) || ':' || substring(jampulang,3,2) as jam from ".static::table('lv_jamhadir')." order by jamdatang desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getJamHadir($conn){
			$sql = "select kodejamhadir, jamdatang, jampulang from ".static::table('lv_jamhadir')." order by jamdatang desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data['jamdatang'][$row['kodejamhadir']] = $row['jamdatang'];
				$a_data['jampulang'][$row['kodejamhadir']] = $row['jampulang'];
			}
			
			return $a_data;
		}
		
		function getKodeShift($conn){
			$sql = "select cast(substring(kodekelkerja,4,4) as int)+1 from ".static::table('ms_kelkerja')." order by kodekelkerja desc limit 1";
			
			$inc =  $conn->GetOne($sql);
			if (empty($inc))
				$kode = 'SHF0001';
			else
				$kode = 'SHF'.str_pad($inc,'4','0', STR_PAD_LEFT);
			
			return $kode;
		}
		
		function getDayPresensi(){
			//digunakan untuk menghubungkan dengan nama field
			$a_day = array("0" => "minggu", "1" => "senin", "2" => "selasa", "3" => "rabu", "4" => "kamis", "5" => "jumat", "6" => "sabtu");
			
			$a_nameday = array("minggu" => "0", "senin" => "1", "selasa" => "2", "rabu" => "3", "kamis" => "4", "jumat" => "5", "sabtu" => "6");
			
			return array("aday" => $a_day, "nameday" => $a_nameday);
		}
		
		//mendapatkan nama lengkap berdasarkan no hand key
		function getNamaLengkap($conn){
			$sql = "select nik,".static::schema.".f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
					from ".static::table('ms_pegawai')."
					where nik is not null";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['nik']] = $row['namalengkap'];
			}
			
			return $a_data;
		}
		
		/**************************************************** CRON SCRIPT ******************************************************/		
		function isAlpaKemarin($conn,$tglkemarin){
			$sql = "select idpegawai
					from ".static::table('pe_presensidet')."
					where tglpresensi='$tglkemarin' and sjamdatang is not null 
					and jamdatang is null and jampulang is null";
			$rs = $conn->Execute($sql);
			
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		/**************************************************** END CRON SCRIPT ******************************************************/
		
		
		/**************************************************** START of HISTORY PRESENSI ******************************************************/
		
		function listQueryRUbahHariKerja($key) {
			$sql = "select * 
					from ".static::table('pe_ubahharikerja')." 
					where idpegawai=$key";
			
			return $sql;
		}
		
		function getCTahunShift($conn){
			$sql = "select date_part('year',tglberlaku) as tahun, date_part('year',tglberlaku) as id 
					from ".static::table('pe_rwtharikerja')."
					group by date_part('year', tglberlaku) order by date_part('year', tglberlaku)";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getLastTahunShift($conn){
			$sql = "select date_part('year',tglberlaku) as tahun from ".static::table('pe_rwtharikerja')." order by date_part('year', tglberlaku) desc limit 1";
			
			return $conn->GetOne($sql);
		}
		
		function listQueryShiftPegawai($r_key,$r_jenisPeg){
			$sql = "select p.idpegawai,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit
					from ".static::table('ms_pegawai')." p
					left join ".static::schema.".ms_unit u on u.idunit=p.idunit
					where p.idjenispegawai = '$r_jenisPeg'";
			return $sql;
		}
		
		function simpanShiftPegawai($conn,$r_key,$r_jenisPeg,$a_shift){
			//hapus dulu
			$conn->Execute("delete from ".static::table('pe_rwtharikerja')." where kodekelkerja = '$r_key' and 
						idpegawai in (select idpegawai from ".static::table('ms_pegawai')." where idjenispegawai = '$r_jenisPeg')");
			
			if(count($a_shift)>0){
				foreach($a_shift as $idpegawai){
					$sql .= "insert into ".static::table('pe_rwtharikerja')." (idpegawai,kodekelkerja,t_username,t_updatetime,t_ipaddress)
							values ($idpegawai,'$r_key',".Query::logInsert().");";
				}
			}
			
			if(!empty($sql))
				$conn->Execute($sql);
			
			return self::saveStatus($conn);
		}
		
		function getJenisPegawai($conn){
			$sql = "select idjenispegawai
					from ".static::table('ms_jenispeg')." j 
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg 
					order by j.idtipepeg limit 1";
			
			return $conn->GetOne($sql);
		}
		
		function getCJenisPegawai($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai
					from ".static::table('ms_jenispeg')." j 
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg 
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listRJadwalGroup($conn, $key, $r_tahun){
			$sql = "select * from ".static::table('v_presensigroup')." where kodekelkerja='$key' and date_part('year',tglberlaku)='$r_tahun' order by tglberlaku";
			
			$a_data = array();
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}
		
		function listRJadwalDate($conn, $key, $r_tahun){
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap 
					from ".static::table('pe_rwtharikerja')." r 
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					where kodekelkerja='$key' and date_part('year',r.tglberlaku)='$r_tahun'";
			
			$a_data = array();
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow())
				$a_data[str_replace('-','',$row['tglberlaku'])][] = $row;
				
			return $a_data;
		}
		
		function infoShift($conn, $key){
			$sql = "select * from ".static::table('ms_kelkerja')." where kodekelkerja='$key'";
			
			return $conn->GetRow($sql);
		}
		
		function setKodeShift($conn, $r_bulan, $r_tahun){
			$r_kode = $r_tahun.str_pad($r_bulan,'2','0', STR_PAD_LEFT);
			$sql = "select count(kodeshift) from ".static::table(pe_rwtshift)." where substring(kodeshift,1,6)='$r_kode'";
			$inc = $conn->GetOne($sql);
			
			if ($inc==0)
				$kodeshift = $r_kode.'01';
			else{
				$inc += 1;
				$kodeshift = $r_kode.str_pad($inc,'2','0', STR_PAD_LEFT);
			}
			
			return $kodeshift;
		}
		
		function getDetailShiftBulan($conn, $r_key){
			$sql = "select substring(kodeshift,1,4) as tahun, substring(kodeshift,5,2) as bulan,* 
					from ".static::table('pe_rwtshift')." where kodeshift='$r_key'";
			
			$a_info = $conn->GetRow($sql);
			
			$sql = "select r.*,k.namakelshift,k.jamdatang,k.jampulang,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
					from ".static::table('pe_rwtshiftdet')." r
					left join ".static::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".static::table('ms_kelshift')." k on k.kodekelshift = r.kodekelshift
					where kodeshift='$r_key' order by k.jamdatang";
			$rs = $conn->Execute($sql);
			$a_data = array();
			$a_detail = array();
			while($row = $rs->FetchRow()){
				$a_data[$row['tglshift']][$row['kodekelshift']][$row['idpegawai']] = $row;
				$a_detail[$row['tglshift']][] = $row['idpegawai'];
			}
			return array("info" => $a_info, "data" => $a_data, "list" => $a_list, "detail" => $a_detail);
		}
		
		function cekDataPrint($conn,$r_key){
			$cekPrint = $conn->GetOne("select 1 from ".static::table('pe_rwtshiftdet')." p where kodeshift='$r_key'");
			
			return $cekPrint;
		}
		
		function listPegawaiKeamanan($conn,$r_key,$r_date){
			$sql = "select p.idpegawai,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap, s.kodekelshift
					from ".static::table('ms_pegawai')." p
					left join ".static::table('pe_rwtshiftdet')." s on s.idpegawai = p.idpegawai and s.tglshift = '$r_date' and s.kodeshift = '$r_key'
					where p.idjenispegawai = 'K2' ";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[] = $row;
			}
			
			return $a_data;
		}
		
		function getCJamShift($conn){
			$sql = "select kodekelshift, namakelshift || ' : ' || jamdatang || ' - ' || jampulang
					from ".static::table('ms_kelshift')." order by jamdatang";
			
			return Query::arrQuery($conn, $sql);
		}
				
		/**************************************************** END of HISTORY PRESENSI ******************************************************/
		
		
		/**************************************************** L E M B U R ******************************************************/
		// mendapatkan kueri list untuk setting lembur
		function listQuerySetLembur() {
			$sql = "select * from ".static::table('ga_lembur');
			
			return $sql;
		}
		
		function getKodeLembur($conn){
			$sql = "select top 1 cast(substring(kodelembur,2,4) as int) from ".static::table('ga_lembur')." order by kodelembur desc";
			
			$inc =  $conn->GetOne($sql);
			if (empty($inc))
				$kode = 'L0001';
			else
				$kode = 'L'.str_pad($inc,'4','0', STR_PAD_LEFT);
			
			return $kode;
		}
		
		function listQueryRSuratLembur($key){
			$sql = "select s.*, coalesce(p.nik ||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) || ' - '|| st.jabatanstruktural as namapejabat, 
					namaunit
					from ".static::table('pe_suratlembur')." s
					left join ".static::schema()."ms_pegawai p on p.idpegawai=s.pejabatatasan
					left join ".static::schema()."ms_struktural st on st.idjstruktural=s.idjstruktural
					left join ".static::schema()."ms_unit u on u.idunit=s.idunit
					where s.idpegawai=$key";
			
			return $sql;
		}
		
		function getDataEditLembur($key){
			$sql = "select s.*, coalesce(p.nik ||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)||' - '|| st.jabatanstruktural as pejabat
					from ".static::table('pe_suratlembur')." s
					left join ".static::schema()."ms_pegawai p on p.idpegawai=s.pejabatatasan
					left join ".static::schema()."ms_struktural st on st.idjstruktural=s.idjstruktural
					where s.idsuratlembur=$key";
			
			return $sql;
		}
		
		function getAtasanLembur($conn,$r_key){
			$sql = "select a.idpegawai,coalesce(a.nik ||' - ','')|| a.namalengkap ||' - ' || a.jabatanstruktural as pejabat,a.idjstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::schema()."ms_struktural s on s.email = p.emailatasan
					left join ".static::schema()."v_pejabat a on a.idjstruktural = s.idjstruktural
					where p.idpegawai=$r_key";
			
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		// mendapatkan kueri list untuk lembur kolektif
		function listQueryLemburKol() {
			$sql = "select k.*, coalesce(p.nik || ' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as pejabat,
					s.jabatanstruktural, u.kodeunit||' - '||u.namaunit as namaunit
					from ".static::table('pe_suratlemburkol')." k
					left join ".static::schema()."ms_pegawai p on p.idpegawai=k.pejabatatasan
					left join ".static::schema()."ms_struktural s on s.idjstruktural=k.idjstruktural
					left join ".static::schema()."ms_unit u on u.idunit=k.idunit";
			
			return $sql;
		}
		
		// mendapatkan kueri list untuk lembur detail
		function getListLemburDetail($conn,$r_key) {
			$sql = "select l.*, p.nik,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
					from ".static::table('pe_suratlembur')." l
					left join ".static::schema()."ms_pegawai p on p.idpegawai=l.idpegawai
					where l.refidkolektif = $r_key";
			
			$a_data = $conn->GetArray($sql);
			
			return $a_data;
		}
		
		function getDataLemburDetail($conn,$r_subkey){
			$sql = "select l.*, coalesce(p.nik||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as pegawai
					from ".static::table('pe_suratlembur')." l
					left join ".static::schema()."ms_pegawai p on p.idpegawai=l.idpegawai
					where l.idsuratlembur = $r_subkey";
			
			return $sql;
		}
		
		//lembur kolektif
		function getDataEditLemburKol($key){
			$sql = "select k.*, coalesce(p.nik||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)||' - '||s.jabatanstruktural as pejabat
					from ".static::table('pe_suratlemburkol')." k
					left join ".static::schema()."ms_pegawai p on p.idpegawai=k.pejabatatasan
					left join ".static::schema()."ms_struktural s on s.idjstruktural=k.idjstruktural
					where k.idsuratkol=$key";
			
			return $sql;
		}
		
		//mendapatkan data lembur kolektif
		function getDataKol($conn,$r_key){
			$sql = "select idjstruktural,pejabatatasan,idunit,tglpenugasan from ".static::table('pe_suratlemburkol')." where idsuratkol = $r_key";
			
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		//mendapatkan daftar validasi lembur
		function listQueryValLembur(){
			$sql = "select k.*, k.sjamawal || ' - ' || k.sjamakhir as jamnormal, k.jamawal || ' - ' || k.jamakhir as jamlembur,
					coalesce(p.nik||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					u.namaunit,pr.isvalid,pr.issetujuatasan, pr.totlembur,
					DATE_PART('hour', k.jamakhir::time - k.jamawal::time) * 60 + DATE_PART('minute', k.jamakhir::time - k.jamawal::time) as totjamlembur
					from ".static::table('pe_suratlembur')." k
					left join ".static::schema()."pe_presensidet pr on pr.idpegawai=k.idpegawai and k.tgllembur=pr.tglpresensi
					left join ".static::schema()."ms_pegawai p on p.idpegawai=k.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit";
			
			return $sql;
		}
		
		
		/**************************************************** END of L E M B U R ******************************************************/
		
		/**************************************************** L A P O R A N ******************************************************/
		function getLapPresensi($conn,$r_kodeunit,$r_tahun,$r_bulan,$r_idpegawai,$r_tipe){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select t.idpegawai, v.nik, v.namalengkap, v.namaunit,v.jabatanstruktural 
					from ".static::table('pe_presensidet')." t
					left join ".static::schema()."v_pegawai v on v.idpegawai=t.idpegawai
					where date_part('year',tglpemasukan) = '$r_tahun' and date_part('month',tglpemasukan) = '$r_bulan'";
			
			if(!empty($r_kodeunit))
				$sql .= " and v.infoleft >= ".(int)$col['infoleft']." and v.inforight <= ".(int)$col['inforight']."";
			if(!empty($r_idpegawai))
				$sql .= " and t.idpegawai = $r_idpegawai";
			
			$sql .= " group by t.idpegawai, v.nik, v.namalengkap, v.namaunit,v.jabatanstruktural";
			$rs1 = $conn->Execute($sql);
			
			$a_row = array();
			while ($row = $rs1->FetchRow())
				$a_row[] = $row;
					
			$sql = "select r.*,
					case when jamdatang is not null then substring(jamdatang,1,2) || ':' || substring(jamdatang,3,2) end as jamdatang2,
					case when jamdatang is not null then substring(jampulang,1,2) || ':' || substring(jampulang,3,2) end as jampulang2,
					case when jamdatang is not null then substring(sjamdatang,1,2) || ':' || substring(sjamdatang,3,2) end as sjamdatang2,
					case when jamdatang is not null then substring(sjampulang,1,2) || ':' || substring(sjampulang,3,2) end as sjampulang2,
					menitdatang, menitpulang, totlembur, round(sdm.f_diffmenit(jamdatang, jampulang)/60,2) as jamkerja
					from ".static::schema()."pe_presensidet r
					left join ".static::schema()."ms_pegawai k on k.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=k.idunit
					left join ".static::schema()."ms_jenispeg v on v.idjenispegawai=k.idtipepeg
					left join ".static::schema()."ms_tipepeg z on z.idtipepeg=k.idtipepeg
					where date_part('year',tglpresensi) = '$r_tahun' and date_part('month',tglpresensi) = '$r_bulan' and k.idtipepeg='$r_tipe'";
					
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			if(!empty($r_idpegawai))
				$sql .= " and k.idpegawai = $r_idpegawai";
			$sql .= " order by tglpresensi";
			
			$rs = $conn->Execute($sql);
			
			$a_row1 = array();
			while ($row = $rs->FetchRow())
				$a_row1[$row[idpegawai]][] = $row;
			
			$a_data = array('pegawai' => $a_row1, 'list' => $rs, 'terima' => $a_row, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}	
		
		//tipe pegawai
		function getTipePegawai($conn,$r_tipe){
			$sql="select tipepeg from ".static::schema()."ms_tipepeg where idtipepeg='$r_tipe'";
			$tipepeg=$conn->GetOne($sql);
			
			return $tipepeg;
		}
		
		function getLapStatusHadir($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select t.idpegawai, v.nik, v.namalengkap
					from ".static::table('pe_presensidet')." t
					left join ".static::table('v_pegawai')." v on v.idpegawai=t.idpegawai
					where  v.infoleft >= ".(int)$col['infoleft']." and v.inforight <= ".(int)$col['inforight']." 
					and tglpresensi between '$r_tglmulai' and '$r_tglselesai'
					group by t.idpegawai,namalengkap,v.nik";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			$sql = "select t.*, 
					case when sdm.f_diffmenit(jamdatang, jampulang) < 0 then 0 else sdm.f_diffmenit(jamdatang, jampulang) end as menit,
					case when sdm.f_diffmenit(sjamdatang, sjampulang) < 0 then 0 else sdm.f_diffmenit(sjamdatang, sjampulang) end as smenit
					from ".static::table('pe_presensidet')." t
					left join ".static::schema()."ms_pegawai p on p.idpegawai=t.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					and t.tglpresensi between '$r_tglmulai' and '$r_tglselesai'";
			$rs = $conn->Execute($sql);
			
			$a_statusabsen = array();
			$a_sumreal = array();
			while ($row = $rs->FetchRow()){
				if ($row['kodeabsensi'] == 'T' or $row['kodeabsensi'] == 'H'){
					$a_statusabsen['H'][$row['idpegawai']]++;
					$a_sumreal['H'][$row['idpegawai']] += $row['menit'];
				}else{
					$a_statusabsen[$row['kodeabsensi']][$row['idpegawai']]++;
					if ($row['kodeabsensi'] == 'D' or $row['kodeabsensi'] == 'C')
						$a_sumreal['D'][$row['idpegawai']] += $row['smenit'];
				}			
			}
			
			$mulai=strtotime($r_tglmulai);
			$selesai=strtotime($r_tglselesai);
			$jnefektif=0;
			$jlibur=0;
			$liburan=0;
			$lama=0;
			$arlama=array();
			$i=0;
			
			while($mulai<=$selesai){
				$nefektif=date("w",$mulai);
				if($nefektif == 0 or $nefektif == 6)
					$jnefektif+=1;			
				$mulai+=86400;
				$arlama[$i++]=$mulai;
			}		
			
			//Cek apakah hari cuti dalam hari liburan	
			if(count($arlama)>0){
				$libur = $conn->Execute("select tgllibur from ".self::table('ms_liburdetail')." 
						where tgllibur between '$r_tglmulai' and '$r_tglselesai' and 
						extract(dow from tgllibur) <> 0 and extract(dow from tgllibur) <> 6");

				while($rowl = $libur->FetchRow()){
					if(in_array(strtotime($rowl['tgllibur']),$arlama))
						$jlibur++;
				}
			}
			
			$liburan = $jnefektif+$jlibur; //Mendapatkan hari libur dan non efektif
			$lama = count($arlama)-$liburan;			
			
			$bebannormal = $lama*8;
			
			$a_return = array('list' => $a_data, 'statusabsen' => $a_statusabsen, 'namaunit' => $col['namaunit'], 'jamnyata' => $a_sumreal, 'jamnormal' => $bebannormal);
			
			return $a_return;			
		}	
		
		function repRekapPresensi($conn,$r_kodeunit,$r_tahun,$r_bulan){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit
					from ".static::table('pe_presensi')." r
					left join ".static::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where substring(periode,1,4) = '$r_tahun' and substring(r.periode,5,2) = '".str_pad($r_bulan,'2','0', STR_PAD_LEFT)."'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."";
				
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;	
		}
		
		function repRekapShift($conn,$r_kodeunit,$tglmulai,$tglselesai){
			$tglmulai = CStr::formatDate($tglmulai);
			$tglselesai = CStr::formatDate($tglselesai);
			
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			//list pegawai keamanan
			$sql = "select idpegawai,nik,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
					from ".static::table('ms_pegawai')." where idjenispegawai='K2'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[] = $row;
			}
			
			//list shift 
			$sql = "select r.*,k.namakelshift
					from ".static::table('pe_rwtshiftdet')." r
					left join ".static::table('ms_kelshift')." k on k.kodekelshift = r.kodekelshift
					where r.tglshift between '$tglmulai' and '$tglselesai'";
			$rss = $conn->Execute($sql);
			
			$a_detail = array();
			while($rows = $rss->FetchRow()){
				$a_detail[$rows['idpegawai']][$rows['tglshift']] = $rows['kodekelshift'];
			}
			
			$sql = "select * from ".static::table('ms_kelshift')."";
			$rsk = $conn->Execute($sql);
			
			$a_keterangan = array();
			while($rowk = $rsk->FetchRow()){
				$a_keterangan[] = $rowk;
			}
			
			$ttd = $conn->GetOne("select ".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap 
								from ".static::table('ms_pegawai')." p 
								where idjstruktural='202010'");
			
			$a_data = array('pegawai' => $a_data, 'shift' => $a_detail, 'namaunit' => $col['namaunit'], 'keterangan' => $a_keterangan, 'ttd' => $ttd);
			
			return $a_data;	
		}
		
		/**************************************************** L A P O R A N ******************************************************/
	
	}
?>
