<?php
	// model presensi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = true;
	require_once(Route::getModelPath('model'));
	require_once(Route::getModelPath('cuti'));
	
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
					from ".self::table('pe_presensiinput')." r 
					left join ".self::table('ms_absensi')." a on a.kodeabsensi=r.kodeabsensi
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//rekap presensi
		function listRekapPresensi(){
			$sql = "select r.*,m.nip, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap
					from ".self::table('pe_presensi')." r
					left join ".static::table('ms_pegawai')." m on m.idpegawai=r.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit";
			
			return $sql;
		}
		
		function listQueryMonitor(){
		

			$sql = "select p.*,m.nip, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap,t.idtipepeg,
					case when sdm.f_diffmenit(jamdatang, jampulang)::integer < 60 then 0 else sdm.f_diffmenit(jamdatang, jampulang)::integer end as menit
					from ".static::table('pe_presensidet')." p
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=m.idtipepeg";
			//print_r($sql);
			//die();
			
			return $sql;
		}
		
		function listQueryShiftBulan(){
			$sql = "select * from ".static::table('pe_rwtshift');
			
			return $sql;
		}
		
		function getDataInputPresensi($r_subkey) {
			$sql = "select * from ".self::table('pe_presensiinput')." where nopresensiinput = '$r_subkey'";
			
			return $sql;
		}
		
		//hapus dulu presensi detail
		function deletePresensiDetail($conn,$r_subkey){
			$sql = "select * from ".self::table('pe_presensiinput')." where nopresensiinput = '$r_subkey'";
			$row = $conn->GetRow($sql);
			
			$tglmulai = $row['tglmulai'];
			$tglselesai = !empty($row['tglselesai']) ? $row['tglselesai'] : $row['tglmulai'];
			
			$err = Query::qDelete($conn,self::table('pe_presensidet'),"idpegawai = ".$row['idpegawai']." and tglpresensi between '$tglmulai' and '$tglselesai' and ismanual = 'Y'");
			
			return self::saveStatus($conn);
		}
		
		//simpan presensi detail
		function savePresensiDetail($conn,$r_subkey){
			$sql = "select * from ".self::table('pe_presensiinput')." where nopresensiinput = '$r_subkey'";
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
			
			if ($record['kodeabsensi'] == 'S' or $record['kodeabsensi'] == 'ST' or $record['kodeabsensi'] == 'I' or $record['kodeabsensi'] == 'TB'){
				unset($record['jamdatang']);
				unset($record['jampulang']);
			}
			
			$record['keterangan'] = $row['keterangan'];
			$record['ismanual'] = 'Y';
			while($mulai<=$selesai){
				$record['tglpresensi'] = date("Y-m-d",$mulai);
				
				//cek apakah sudah ada
				$isExist = $conn->GetOne("select 1 from ".self::table('pe_presensidet')." where idpegawai = ".$record['idpegawai']." and tglpresensi = '".$record['tglpresensi']."'");
				
				if(empty($isExist))
					$err = Query::recInsert($conn,$record,self::table('pe_presensidet'));
				else
					$err = Query::recUpdate($conn,$record,self::table('pe_presensidet'),static::getCondition($record['idpegawai'].'|'.$record['tglpresensi'],'idpegawai,tglpresensi'));
					
				if($err)
					break;
					
				$mulai+=86400;
			}
				
			return self::saveStatus($conn);
		}
		
		//menyimpan presensi detail
		function saveDetailPresensi($conn, $record, $tgl, $r_key, $p_dbtable,$p_key){
			$cek = $conn->GetOne("select 1 from ".static::schema.".pe_presensidet where tglpresensi='$tgl' and idpegawai='$r_key'");
			$key = $tgl.'|'.$r_key;
			
			if(empty($cek)){
				$record['tglpresensi'] = $tgl;
				$record['idpegawai'] = $r_key;
				$record['kodeabsensi'] = 'H';
				$record['ismanual'] = 'Y';
				$record['tglpemasukan'] = date('Y-m-d');
				list($p_posterr,$p_postmsg) = self::insertRecord($conn,$record,true,$p_dbtable);
			}else{
				list($p_posterr,$p_postmsg) = self::updateRecord($conn, $record, $key, true, $p_dbtable,$p_key);
			}
			
			return array($p_posterr,$p_postmsg);
		}
		
		function allAbsensi($conn){
			$sql = "select kodeabsensi, absensi,color from ".static::table('ms_absensi')." order by kodeabsensi asc";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodeabsensi']] = $row;
			}
			
			return $a_data;
		}
		
		function jenisAbsensi($conn,$isupdate=false){
			$sql = "select kodeabsensi, absensi from ".static::table('ms_absensi')." where kodeabsensi in ('I','S','ST','H','HL','TB') order by kodeabsensi desc";
			
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
					group by substring(periode,1,4) order by substring(periode,1,4) desc";
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
				return "substring(cast(cast(k.tglpenugasan as date) as varchar),6,2) = '$key'";
			if($col == 'tahun')
				return "substring(cast(cast(k.tglpenugasan as date) as varchar),1,4) = '$key'";
			if($col == 'periode')
				return "substring(r.periode ,1,4) = '$key'";
			if($col == 'tglpresensi')
				return "cast(tglpresensi as date) = '".CStr::formatDate($key)."'";
			if($col == 'tipepeg')
				return "m.idtipepeg ='$key'";
			if($col == 'blnpresensi')
				return "substring(r.periode ,5,2) = '$key'";
			if($col == 'jenisabsensi'){
				if($key != 'all')
					return "p.kodeabsensi='$key'";
				else
					return "(1=1)";
			}	
			if($col == 'validasi'){
				if($key != 'all')
					return "k.isvalid='$key'";
				else
					return "(1=1)";
			}
			if($col == 'persetujuan'){
				if($key != 'all')
					return "issetujuatasan='$key'";
				else
					return "(1=1)";
			}
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
					extract(dow from d.tgllibur) <> 1 and extract(dow from d.tgllibur) <> 7";
					
			$rs = $conn->Execute($sql);
	
			while($row = $rs->FetchRow()){
				$a_data[$row['tgllibur']] = $row;
			}
			
			return $a_data;
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
			$rsdet = $conn->Execute("select tglpresensi,kodeabsensi,jamdatang from ".self::table('pe_presensidet')." where nourutcuti = '$r_key'");
			while($rowdet = $rsdet->FetchRow()){
				if(empty($rowdet['jamdatang']))
					$conn->Execute("update ".self::table('pe_presensidet')." set kodeabsensi = 'A' where idpegawai = ".$rowv['idpegawai']." and tglpresensi = '".$rowdet['tglpresensi']."'");
			}
			$conn->Execute("update ".self::table('pe_presensidet')." set nourutcuti = null where nourutcuti = '$r_key'");
			
			//bila disetujui, maka insert ke presensi
			if($rowv['statususulan'] == 'S'){
				$rsd = $conn->Execute("select * from ".self::table('pe_rwtcutidet')." where nourutcuti = '$r_key' and ischange is null");
				
				while($rowd = $rsd->FetchRow()){
					$mulai=strtotime($rowd['tglmulai']);
					$selesai=strtotime($rowd['tglselesai']);
					
					$artgl=array();
					$i=0;		
					while($mulai<=$selesai){
						$nefektif=date("w",$mulai);
						$tgl = date("Y-m-d",$mulai);
						
						//pengecekan shift
						$shift = mCuti::isJadwalShift($conn,$rowv['idpegawai'],$tgl);
						if($shift == 3){
							if($nefektif != 0 and $nefektif != 6)
								$artgl[$i++] = date('Y-m-d',$mulai);
						}
						else if($shift == 1){
							$artgl[$i++] = date('Y-m-d',$mulai);
						}				
												
						$mulai+=86400;
					}
					
					//cek liburan
					$libur = $conn->Execute("select cast(tgllibur as date) as tgllibur from ".self::table('ms_liburdetail')." 
						where tgllibur between '".$rowd['tglmulai']."' and '".$rowd['tglselesai']."' and 
						datepart(dw,tgllibur) <> '1' and datepart(dw,tgllibur) <> '7'");

					while($rowl = $libur->FetchRow()){
						//pengecekan shift
						$shift = mCuti::isJadwalShift($conn,$rowv['idpegawai'],$rowl['tgllibur']);
						if(($shift == 1 or $shift == 3) and in_array($rowl['tgllibur'],$artgl)){
							$key = array_search($rowl['tgllibur'], $artgl);
							unset($artgl[$key]);
						}
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
			$rsdet = $conn->Execute("select tglpresensi,kodeabsensi,jamdatang from ".self::table('pe_presensidet')." where nodinas = '$r_key'");
			while($rowdet = $rsdet->FetchRow()){
				if(empty($rowdet['jamdatang']))
					$conn->Execute("update ".self::table('pe_presensidet')." set kodeabsensi = 'A' where idpegawai = ".$row['pegditunjuk']." and tglpresensi = '".$rowdet['tglpresensi']."'");
			}
			$conn->Execute("update ".self::table('pe_presensidet')." set nodinas = null where nodinas = '$r_key'");
			
			//bila disetujui, maka insert ke presensi
			if($row['issetujuatasan'] == 'S' and $row['issetujuwarek2'] == 'S' and $row['issetujukabagkeu'] == 'S' and $row['issetujukasdm'] == 'S'){
				$mulai=strtotime($row['tglpergi']);
				$selesai=strtotime($row['tglpulang']);

				$artgl=array();
				$i=0;		
				while($mulai<=$selesai){
					$artgl[$i++] = date('Y-m-d',$mulai);					
					$mulai+=86400;
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
			
			return self::saveStatus($conn);
		}
		
		function deleteFormDinas($conn,$r_key){
			//hapus data yang salah input murni dinas
			$conn->Execute("delete from ".self::table('pe_presensidet')." where nodinas = '$r_key' and kodeabsensi = 'D' and jamdatang is null and jampulang is null and sjamdatang is null and sjampulang is null");
			
			//hapus data misal lewat dari tanggal sekarang
			$conn->Execute("delete from ".self::table('pe_presensidet')." where nodinas = '$r_key' and kodeabsensi = 'D' and tglpresensi > '".date('Y-m-d')."'");
			
			//cek kehadiran
			$rsdet = $conn->Execute("select * from ".self::table('pe_presensidet')." where nodinas = '$r_key'");
			while($rowdet = $rsdet->FetchRow()){
				if(empty($rowdet['jamdatang']) and !empty($rowdet['sjamdatang']) and !empty($rowdet['sjampulang']))
					$conn->Execute("update ".self::table('pe_presensidet')." set kodeabsensi = 'A' where idpegawai = ".$rowdet['idpegawai']." and tglpresensi = '".$rowdet['tglpresensi']."'");

				if(!empty($rowdet['jamdatang']) and !empty($rowdet['sjamdatang']) and !empty($rowdet['sjampulang']))
					$conn->Execute("update ".self::table('pe_presensidet')." set kodeabsensi = 'H' where idpegawai = ".$rowdet['idpegawai']." and tglpresensi = '".$rowdet['tglpresensi']."'");
			}

			//update null
			$conn->Execute("update ".self::table('pe_presensidet')." set nodinas = null,keterangan=null where nodinas = '$r_key'");
			
			return self::deleteStatus($conn);
		}
		
		function saveDetailShift($conn,$idpegawai,$tglmulai,$tglselesai,$kelkerja){
			$sql = "delete from ".static::table('pe_presensidet')." where idpegawai=$idpegawai and tglpresensi<cast(getdate() as varchar)";
			$conn->Execute($sql);
			
			$tglmulai = date("Y-m-d");
			
			$a_ahari = array();
			$a_ahari = mPresensi::getDayPresensi();
			$a_hari = array();
			$a_hari = $a_ahari['aday'];
			
			$a_jamhadir = array();
			$a_jamhadir = mPresensi::getJamHadir($conn);
			
			$sql ="select DATEPART(dw, dt) as nohari,dt as tgl,senin,selasa,rabu,kamis,jumat,sabtu,minggu
					from sdm.f_generatedates('$tglmulai','$tglselesai') g
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
					where idpegawai=$col[idpegawai] and tglpresensi between cast(getdate() as date) and '$col[tglakhir]'";
			$conn->Execute($sql);
		}
		
		function saveDetailShiftBulan($conn,$idpegawai,$r_kode){
			$sql = "delete from ".static::table('pe_presensidet')." where idpegawai=$idpegawai and tglpresensi<cast(getdate() as varchar)";
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
			$a_message = mPresensi::delete($conn,($r_key.'|'.$rkey),'pe_rwtshiftpeg','kodeshift,idpegawai');
			return $a_message;
		}
		
		function deleteShiftBulan($conn,$r_key){			
			mPresensi::delete($conn,$r_key,'pe_rwtshiftpeg','kodeshift');
			mPresensi::delete($conn,$r_key,'pe_rwtshiftdet','kodeshift');
			$a_message = mPresensi::delete($conn,$r_key,'pe_rwtshift','kodeshift');
			
			return $a_message;
		}
		
		// mendapatkan kueri list untuk setting kehadiran
		function listQuerySetKehadiran() {
			$sql = "select * from ".static::table('ms_keterlambatan');
			
			return $sql;
		}
		
		//mendapatkan jumlah hari kerja pegawai selama sebulan
		function getHariKerja($conn){
			$sql = "select jmlharikerja from ".static::table('ms_keterlambatan')." order by tglberlaku desc limit 1";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan kueri list untuk setting lembur
		function listQueryShift() {
			$sql = "select * from ".static::table('ms_kelkerja');
			
			return $sql;
		}
		
		function getPresensiDate($conn, $date){
			$sql = "select idpegawai from ".static::table('pe_presensidet')." where tglpresensi='$date'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data['idpegawai'][$row['idpegawai']] = $row['idpegawai'];
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
		
		function getCAbsensi($conn){
			$sql = "select kodeabsensi, absensi from ".static::table('ms_absensi')." order by kodeabsensi asc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodeabsensi']] = $row['absensi'];
			}
			
			return $a_data;
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
			$sql = "select substring(kodekelkerja,4,4)+1 from ".static::table('ms_kelkerja')." order by kodekelkerja desc limit 1";
			
			$inc =  $conn->GetOne($sql);
			if (empty($inc))
				$kode = 'SHF0001';
			else
				$kode = 'SHF'.str_pad($inc,'4','0', STR_PAD_LEFT);
			
			return $kode;
		}
		
		function getDayPresensi(){
			//digunakan untuk menghubungkan dengan nama field
			$a_day = array("1" => "minggu", "2" => "senin", "3" => "selasa", "4" => "rabu", "5" => "kamis", "6" => "jumat", "7" => "sabtu");
			
			$a_nameday = array("minggu" => "1", "senin" => "2", "selasa" => "3", "rabu" => "4", "kamis" => "5", "jumat" => "6", "sabtu" => "7");
			
			return array("aday" => $a_day, "nameday" => $a_nameday);
		}
		
		//mendapatkan nama lengkap berdasarkan no hand key
		function getNamaLengkap($conn){
			$sql = "select nip,".static::schema.".f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
					from ".static::table('ms_pegawai')."
					where nip is not null";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['nip']] = $row['namalengkap'];
			}
			
			return $a_data;
		}
		
		function getCVal(){	
			$a_data = array('all' => '-- Semua Status --','Y'=>'Ya','T'=>'Tidak');
			
			return $a_data;
		}
		
		// save Upload Presensi
		function saveUploadPresensi($conn,$record,$key,$status=false,$table,$colkey) {
			$sql = "select 1 from ".static::table($table)." where ".static::getCondition($key,$colkey);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return static::insertRecord($conn,$record,$status,$table,'',$colkey);
			else
				return static::updateRecord($conn,$record,$key,$status,$table,$colkey);
		}
		
		/**************************************************** CRON SCRIPT ******************************************************/
		function getListCronPresensi($conn){			
			$sql = "select r.idpegawai,cast(getdate() as date)  as tglsekarang, DATEPART(dw, GETDATE()) as nohari, k.* from sdm.pe_rwtharikerja r
					left join sdm.ms_kelkerja k on k.kodekelkerja=r.kodekelkerja 
					where GETDATE() BETWEEN tglawal and tglakhir";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function isLibur($conn,$tglkemarin){
			$sql = "select tgllibur from ".static::table('ms_liburdetail')." where tgllibur = '$tglkemarin'";
			$islibur = $conn->GetOne($sql);

			return $islibur;
		}
		
		function cekShift($conn,$tglkemarin){
			$kemarin = strtotime($tglkemarin);
			$elemen=date("w",$kemarin);
			
			//select dari master pegawai
			$sql = "select p.idpegawai from ".static::table('ms_pegawai')." p
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif = p.idstatusaktif
					where a.iskeluar <> 'Y'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$sql = "select kodekelkerja from ".static::table('pe_rwtharikerja')." 
						where idpegawai = ".$row['idpegawai']." and tglberlaku <= '$tglkemarin' and isaktif='Y' order by tglberlaku desc limit 1";
				$kode = $conn->GetOne($sql);
				
				if(!empty($kode)){
					$sql = "select case when $elemen = 0 then minggu when $elemen = 1 then senin when $elemen = 2 then selasa
							when $elemen = 3 then rabu when $elemen = 4 then kamis when $elemen = 5 then jumat when $elemen = 6 then sabtu end
							from ".static::table('ms_kelkerja')." 
							where kodekelkerja = '$kode'";
					$hari = $conn->GetOne($sql);
					
					if(!empty($hari)){
						$rows = $conn->GetRow("select jamdatang,jampulang from ".static::table('lv_jamhadir')." where kodejamhadir = '$hari'");
						$a_kode[$row['idpegawai']]['jamdatang'] = $rows['jamdatang'];
						$a_kode[$row['idpegawai']]['jampulang'] = $rows['jampulang'];
					}
				}
				
				if(empty($kode)){
					$sql = "select jamdatang,jampulang from ".static::table('v_pegawaishift')." where idpegawai = ".$row['idpegawai']." and tglshift = '$tglkemarin'";
					$rowd = $conn->GetRow($sql);
					
					if(!empty($rowd)){
						$a_kode[$row['idpegawai']]['jamdatang'] = $rowd['jamdatang'];
						$a_kode[$row['idpegawai']]['jampulang'] = $rowd['jampulang'];
					}
				}
			}
			
			return $a_kode;
		}
		
		function isAlpaKemarin($conn,$tglkemarin){
			//pengecekan shift
			$a_kode = self::cekShift($conn,$tglkemarin);
			
			//yang presensi						
			$sql = "select idpegawai from ".static::table('pe_presensidet')."
					where tglpresensi='$tglkemarin'";
			$rs = $conn->Execute($sql);
			
			while ($row = $rs->FetchRow())
				$a_pres[] = $row['idpegawai'];
				
			//pengecekan apakah alpa
			foreach($a_kode as $key => $val){
				if(!in_array($key,$a_pres))
					$a_datap['insert'][$key] = $val;
			}
			
			return $a_datap;
		}
		
		function isDatangKosong($conn,$tglkemarin){
			//pengecekan shift
			$a_kode = self::cekShift($conn,$tglkemarin);
						
			$sql = "select idpegawai from ".static::table('pe_presensidet')."
					where tglpresensi='$tglkemarin' and (sjamdatang is not null or sjamdatang is not null)
					and jamdatang is null and jampulang is not null and nourutcuti is null and nodinas is null and ismanual is null";
			$rs = $conn->Execute($sql);
			
			while ($row = $rs->FetchRow())
				$a_data[$row['idpegawai']] = $row['idpegawai'];
			
			return $a_data;
		}
		
		function isPulangKosong($conn,$tglkemarin){
			//pengecekan shift
			$a_kode = self::cekShift($conn,$tglkemarin);
						
			$sql = "select idpegawai from ".static::table('pe_presensidet')."
					where tglpresensi='$tglkemarin' and (sjamdatang is not null or sjamdatang is not null)
					and jamdatang is not null and jampulang is null and nourutcuti is null and nodinas is null and ismanual is null";
			$rs = $conn->Execute($sql);
			
			while ($row = $rs->FetchRow())
				$a_data[$row['idpegawai']] = $row['idpegawai'];
			
			return $a_data;
		}
		
		function updatePotongan($conn,$tglkemarin){
			$sql = "update ".static::table('pe_presensidet')." set procpotkehadirantelat=null,procpotkehadiranpd=null,
					procpottransporttelat=null,procpottransportpd=null
					where tglpresensi = '$tglkemarin'";
			$conn->Execute($sql);
		}
		
		/*
		//periode tarif potongan
		function getPeriodePot($conn){
			$sql = "select top 1 periodetarif from ".static::table('ms_periodetarif')." order by tglmulai desc";
			
			return $conn->GetOne($sql);
		}
		
		//data gaji pegawai
		function dataGajiPeg($conn){
			$sql = "select p.idpangkat,p.idtipepeg,p.idjenispegawai,p.idhubkerja,p.idpegawai 
					from ".static::table('ms_pegawai')." p
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif = p.idstatusaktif
					where a.iskeluar <> 'Y'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_peg['PANGKAT'][$row['idpegawai']] = $row['idpangkat'];
				$a_peg['TIPEPEG'][$row['idpegawai']] = $row['idtipepeg'];
				$a_peg['JENISPEG'][$row['idpegawai']] = $row['idjenispegawai'];
				$a_peg['HUBKERJA'][$row['idpegawai']] = $row['idhubkerja'];
			}
		}
		
		function potonganPresensi($conn,$tglkemarin){
			//periode potongan
			$periodepot = self::getPeriodePot($conn);
			
			$sql = "select * from from ".static::table('pe_presensidet')." where tglpresensi='$tglkemarin'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				if($a_peg['TIPEPEG'][$row['idpegawai']] == 'A' or $a_peg['TIPEPEG'][$row['idpegawai']] == 'AD')
					$tarifpotkehadiran = $conn->GetOne("select sdm.f_tariftunjangan('$periodepot','T00004','".$a_peg['TIPEPEG'][$row['idpegawai']]."')");
			}
		}
		*/
		
		/**************************************************** END CRON SCRIPT ******************************************************/
		
		
		/**************************************************** START of HISTORY PRESENSI ******************************************************/
		
		function listQueryRUbahHariKerja($key) {
			$sql = "select *,sjamdatangubah||' - '||sjampulangubah as jamlama,sjamdatang||' - '||sjampulang as jambaru
					from ".static::table('pe_ubahharikerja')." 
					where idpegawai=$key";
			
			return $sql;
		}
		
		function getCTahunShift($conn){
			$sql = "select * 
					from ".static::table('pe_rwtharikerja')."
					";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getLastTahunShift($conn){
			$sql = "select  extract(year from tglberlaku) as tahun from ".static::table('pe_rwtharikerja')." order by extract(year from tglberlaku) desc limit 1";
			
			return $conn->GetOne($sql);
		}
		
		function listRJadwalGroup($conn, $key, $r_tahun){
			

			$sql = "SELECT r.kodekelkerja,
    k.keterangan,
    r.tglberlaku
   FROM ".static::table('pe_rwtharikerja')." r
     LEFT JOIN ".static::table('ms_kelkerja')." k ON k.kodekelkerja::text = r.kodekelkerja::text where r.kodekelkerja='$key' 
  GROUP BY r.kodekelkerja, k.keterangan, r.tglberlaku order by r.tglberlaku";
			
			$a_data = array();
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}
		
		function listRJadwalDate($conn, $key, $r_tahun){
			$sql = "select r.*,p.nip,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap 
					from ".static::table('pe_rwtharikerja')." r 
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					where kodekelkerja='$key' ";
			
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
			$sql = "select max(cast(substring(kodeshift,7,2) as int)) from ".static::table('pe_rwtshift')." where substring(kodeshift,1,6)='$r_kode'";
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
			
			$sql = "select kodeshift,tglshift,jamdatang,jampulang from ".static::table('pe_rwtshiftdet')." where kodeshift='$r_key'";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[$row['tglshift']]['kode'] = $row['kodeshift'];
				$a_data[$row['tglshift']]['date'] = $row['tglshift'];
				$a_data[$row['tglshift']]['jamdatang'] = $row['jamdatang'];
				$a_data[$row['tglshift']]['jampulang'] = $row['jampulang'];
			}
			
			$sql = "select p.nip,t.idpegawai,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
					from ".static::table('pe_rwtshiftpeg')." t 
					left join ".static::table('ms_pegawai')." p on p.idpegawai=t.idpegawai
					where kodeshift='$r_key'";
			$rs = $conn->Execute($sql);
			$a_list = array();
			while($row = $rs->FetchRow())
				$a_list[] = $row;
			
			return array("info" => $a_info, "data" => $a_data, "list" => $a_list);
		}
				
		/**************************************************** END of HISTORY PRESENSI ******************************************************/
		
		
		/**************************************************** L E M B U R ******************************************************/
		// mendapatkan kueri list untuk setting lembur
		function listQuerySetLembur() {
			$sql = "select * from ".static::table('ms_lembur');
			
			return $sql;
		}
		
		function getKodeLembur($conn){
			$sql = "select substring(kodelembur,2,4) from ".static::table('ms_lembur')." order by kodelembur desc limit 1";
			
			$inc =  $conn->GetOne($sql);
			if (empty($inc))
				$kode = 'L0001';
			else
				$kode = 'L'.str_pad($inc,'4','0', STR_PAD_LEFT);
			
			return $kode;
		}
		
		function listQueryRSuratLembur($key){
			$sql = "select s.*, coalesce(p.nip||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)||' - '||st.jabatanstruktural as namapejabat, 
					namaunit,substring(jamawal,1,2)||':'||substring(jamawal,3,2)||' - '||substring(jamakhir,1,2)||':'||substring(jamakhir,3,2) as jamlembur
					from ".static::table('pe_suratlembur')." s
					left join ".static::schema()."ms_pegawai p on p.idpegawai=s.pejabatatasan
					left join ".static::schema()."ms_struktural st on st.idjstruktural=s.idjstruktural
					left join ".static::schema()."ms_unit u on u.idunit=s.idunit
					where s.idpegawai=$key";
			
			return $sql;
		}
		
		function getDataEditLembur($key){
			$sql = "select s.*, coalesce(p.nip||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)||' - '||st.jabatanstruktural as pejabat,namaunit
					from ".static::table('pe_suratlembur')." s
					left join ".static::schema()."ms_pegawai p on p.idpegawai=s.pejabatatasan
					left join ".static::schema()."ms_unit u on s.idunit=u.idunit
					left join ".static::schema()."ms_struktural st on st.idjstruktural=s.idjstruktural
					where s.idsuratlembur=$key";
			
			return $sql;
		}
		
		function getAtasanLembur($conn,$r_key){
			$sql = "select a.idpegawai,coalesce(a.nip||' - ','')|| a.namalengkap + ' - ' + a.jabatanstruktural as pejabat,a.idjstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::schema()."ms_struktural s on s.email = p.emailatasan
					left join ".static::schema()."v_pejabat a on a.idjstruktural = s.idjstruktural
					where p.idpegawai=$r_key";
			
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		// mendapatkan kueri list untuk lembur kolektif
		function listQueryLemburKol() {
			$sql = "select k.*, coalesce(p.nip||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as pejabat,
					s.jabatanstruktural, u.kodeunit||' - '||u.namaunit as namaunit
					from ".static::table('pe_suratlemburkol')." k
					left join ".static::schema()."ms_pegawai p on p.idpegawai=k.pejabatatasan
					left join ".static::schema()."ms_struktural s on s.idjstruktural=k.idjstruktural
					left join ".static::schema()."ms_unit u on u.idunit=k.idunit";
			
			return $sql;
		}
		
		// mendapatkan kueri list untuk lembur detail
		function getListLemburDetail($conn,$r_key) {
			$sql = "select l.*, p.nip,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
					from ".static::table('pe_suratlembur')." l
					left join ".static::schema()."ms_pegawai p on p.idpegawai=l.idpegawai
					where l.refidkolektif = $r_key";
			
			$a_data = $conn->GetArray($sql);
			
			return $a_data;
		}
		
		function getDataLemburDetail($conn,$r_subkey){
			$sql = "select l.*, coalesce(p.nip||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as pegawai
					from ".static::table('pe_suratlembur')." l
					left join ".static::schema()."ms_pegawai p on p.idpegawai=l.idpegawai
					where l.idsuratlembur = $r_subkey";
			
			return $sql;
		}
		
		//lembur kolektif
		function getDataEditLemburKol($key){
			$sql = "select k.*, coalesce(p.nip||' - ','')||".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)||' - '||s.jabatanstruktural as pejabat
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
			$sql = "select k.idsuratlembur, pr.sjamdatang || ' - ' || pr.sjampulang as jamnormal, k.jamawal || ' - ' || k.jamakhir as jamlembur, pr.jamdatang || ' - ' || pr.jampulang as jamreal, 
					".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					u.namaunit,tglpenugasan,pr.isvalid,pr.issetujuatasan, case when sdm.f_diffmenit(jamawal,jamakhir) < 0 then 0 else sdm.f_diffmenit(jamawal,jamakhir) end as totallembur, pr.totlembur,
					pr.idpegawai, pr.tglpresensi, k.tgllembur
					from ".static::table('pe_suratlembur')." k
					left join ".static::schema()."pe_presensidet pr on pr.idpegawai=k.idpegawai and k.tgllembur=pr.tglpresensi
					left join ".static::schema()."ms_pegawai p on p.idpegawai=k.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=k.idunit
					where pr.jamdatang is not null and pr.jampulang is not null";
			
			return $sql;
		}
		
		
		/**************************************************** END of L E M B U R ******************************************************/
		
		/**************************************************** L A P O R A N ******************************************************/
		function getLapPresensi($conn,$r_kodeunit,$r_tahun,$r_bulan,$r_idpegawai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select t.idpegawai, v.nip, v.namalengkap, v.namaunit,v.jabatanstruktural 
					from ".static::table('pe_presensidet')." t
					left join ".static::schema()."v_pegawai v on v.idpegawai=t.idpegawai
					where datepart(year,tglpresensi) = '$r_tahun' and datepart(month,tglpresensi) = '$r_bulan'";
			
			if(!empty($r_kodeunit))
				$sql .= " and v.infoleft >= ".(int)$col['infoleft']." and v.inforight <= ".(int)$col['inforight']."";
			if(!empty($r_idpegawai))
				$sql .= " and t.idpegawai = $r_idpegawai";
			
			$sql .= " group by t.idpegawai, v.nip, v.namalengkap, v.namaunit,v.jabatanstruktural";
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
					where datepart(year,tglpresensi) = '$r_tahun' and datepart(month,tglpresensi) = '$r_bulan'";
					
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

		function getLapPresensiTemp($conn,$r_kodeunit,$r_tahun,$r_bulan,$r_idpegawai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select t.idpegawai, v.nip, v.namalengkap, v.namaunit,v.jabatanstruktural 
					from ".static::table('pe_presensidettemp')." t
					left join ".static::schema()."v_pegawai v on v.idpegawai=t.idpegawai
					where datepart(year,tglpresensi) = '$r_tahun' and datepart(month,tglpresensi) = '$r_bulan'";
			
			if(!empty($r_kodeunit))
				$sql .= " and v.infoleft >= ".(int)$col['infoleft']." and v.inforight <= ".(int)$col['inforight']."";
			if(!empty($r_idpegawai))
				$sql .= " and t.idpegawai = $r_idpegawai";
			
			$sql .= " group by t.idpegawai, v.nip, v.namalengkap, v.namaunit,v.jabatanstruktural";
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
					from ".static::schema()."pe_presensidettemp r
					left join ".static::schema()."ms_pegawai k on k.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=k.idunit
					left join ".static::schema()."ms_jenispeg v on v.idjenispegawai=k.idtipepeg
					left join ".static::schema()."ms_tipepeg z on z.idtipepeg=k.idtipepeg
					where datepart(year,tglpresensi) = '$r_tahun' and datepart(month,tglpresensi) = '$r_bulan'";
					
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
		
		//filter hubungan kerja 
		function filterHubKerja($conn){
			$sql = "select idhubkerja, hubkerja from ".static::table('ms_hubkerja')." order by idhubkerja";
			
			return Query::arrQuery($conn, $sql);
		}
			
		
		function getLapStatusHadir($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$sqlhubkerja){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select t.idpegawai, v.nip, v.namalengkap
					from ".static::table('pe_presensidet')." t
					left join ".static::table('v_pegawai')." v on v.idpegawai=t.idpegawai
					left join ".static::table('ms_pegawai')." p on p.idpegawai=t.idpegawai
					where  v.infoleft >= ".(int)$col['infoleft']." and v.inforight <= ".(int)$col['inforight']." {$sqlhubkerja}  
					and tglpresensi between '$r_tglmulai' and '$r_tglselesai'
					group by t.idpegawai,namalengkap,v.nip";
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
				if ($row['kodeabsensi'] == 'T' or $row['kodeabsensi'] == 'H' or $row['kodeabsensi'] == 'PD' or $row['kodeabsensi'] == 'HL'){
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
						datepart(dw,tgllibur) <> 1 and datepart(dw,tgllibur) <> 7");

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
			
			$sql = "select r.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
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
		
		function repRekapLembur($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$jenispeg){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,p.idunit,u.namaunit,".static::schema.".f_namalengkap(pa.gelardepan,pa.namadepan,pa.namatengah,pa.namabelakang,pa.gelarbelakang) as pimpinan
					from ".static::table('pe_suratlembur ')." r
					left join ".static::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::schema()."ms_pegawai pa on pa.idpegawai=r.pejabatatasan
					left join ".static::schema()."ms_struktural st on st.idjstruktural=r.idjstruktural
					where tgllembur between '$r_tglmulai' and '$r_tglselesai' and p.idjenispegawai in ('$jenispeg')
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." and r.jmljam is not null
					order by u.infoleft,r.idpegawai,r.tgllembur,r.jamawal";
				
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_rowspan[$row['idpegawai']]++;

				$a_data[$row['idsuratlembur']]['idpegawai'] = $row['idpegawai'];
				$a_data[$row['idsuratlembur']]['nip'] = $row['nip'];
				$a_data[$row['idsuratlembur']]['namapegawai'] = $row['namapegawai'];
				$a_data[$row['idsuratlembur']]['idunit'] = $row['idunit'];
				$a_data[$row['idsuratlembur']]['namaunit'] = $row['namaunit'];
				$a_data[$row['idsuratlembur']]['jenislembur'] = $row['jenislembur'];
				$a_data[$row['idsuratlembur']]['tgllembur'] = $row['tgllembur'];
				$a_data[$row['idsuratlembur']]['tglpenugasan'] = $row['tglpenugasan'];
				$a_data[$row['idsuratlembur']]['jmljam'] = $row['jmljam'];
				$a_data[$row['idsuratlembur']]['jamawal'] = $row['jamawal'];
				$a_data[$row['idsuratlembur']]['jamakhir'] = $row['jamakhir'];
				$a_data[$row['idsuratlembur']]['pimpinan'] = $row['pimpinan'];
				$a_data[$row['idsuratlembur']]['lokasi'] = $row['lokasi'];
			}
			$a_data = array('list' => $a_data, 'rowspan' => $a_rowspan, 'namaunit' => $col['namaunit']);
			
			return $a_data;	
		}
		
		function repRekapShift($conn,$r_kodeunit,$r_mulai,$r_selesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$a_ahari = array();
			$a_ahari = mPresensi::getDayPresensi();
			$a_hari = array();
			$a_hari = $a_ahari['aday'];
			
			$a_jamhadir = array();
			$a_jamhadir = mPresensi::getJamHadir($conn);
			
			$sql = "select r.kodekelkerja,k.keterangan,senin,selasa,rabu,kamis,jumat,sabtu,minggu from ".static::table('pe_rwtharikerja')." r
					left join ".static::table('ms_kelkerja')." k on k.kodekelkerja=r.kodekelkerja 
					where tglberlaku between '$r_mulai' and '$r_selesai' 
					group by r.kodekelkerja,k.keterangan,senin,selasa,rabu,kamis,jumat,sabtu,minggu";
			$rs = $conn->Execute($sql);
			$a_jamkerja = array();
			$a_kelkerja = array();
			while ($row = $rs->FetchRow()){
				$a_kelkerja[$row['kodekelkerja']] = $row['keterangan'];
				foreach($a_hari as $hari){
					$a_jamkerja[$row['kodekelkerja']][$hari]['datang'] = $a_jamhadir['jamdatang'][$row[$hari]];
					$a_jamkerja[$row['kodekelkerja']][$hari]['pulang'] = $a_jamhadir['jampulang'][$row[$hari]];
				}
			}
			
			$sql = "select p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,k.kodekelkerja
					from ".static::table('ms_pegawai')." p
					left join ".static::table('pe_rwtharikerja')." k on p.idpegawai=k.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where tglberlaku between '$r_mulai' and '$r_selesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."";
			$rs = $conn->Execute($sql);
			$a_list = array();
			while ($row = $rs->FetchRow()){
				$a_list[$row['kodekelkerja']][] = $row;
			}
			
			$a_data = array('list' => $a_list, 'kelkerja' => $a_kelkerja, 'shift' => $a_jamkerja, 'hari' => $a_hari, 'namaunit' => $col['namaunit']);
			
			return $a_data;	
		}

		function repRekapShiftPegawai($conn,$r_kodeunit,$r_idpegawai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);

			//select pegawai yang masih aktif
			$sql = "select p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,p.idpegawai
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif=p.idstatusaktif
					where a.iskeluar = 'T'";
			if(!empty($r_idpegawai))
				$sql .= " and p.idpegawai = $r_idpegawai";
			else
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);

			//data shift pegawai
			$a_jamhadir = mPresensi::getJamHadir($conn);
			$a_ahari = mPresensi::getDayPresensi();
			$a_hari = $a_ahari['aday'];

			$sql = "select r.idpegawai,r.kodekelkerja,r.tglberlaku,k.keterangan,k.senin,k.selasa,k.rabu,k.kamis,k.jumat,k.sabtu,k.minggu 
					from ".static::table('pe_rwtharikerja')." r
					left join ".static::table('ms_kelkerja')." k on k.kodekelkerja=r.kodekelkerja
					left join ".static::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif=p.idstatusaktif
					where a.iskeluar = 'T' and r.isaktif = 'Y'";
			if(!empty($r_idpegawai))
				$sql .= " and p.idpegawai = $r_idpegawai";
			else
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$sql .= " order by r.tglberlaku";
			$rsh = $conn->Execute($sql);

			while($rowh = $rsh->FetchRow()){
				$a_jamkerja[$rowh['idpegawai']][$rowh['kodekelkerja']][$rowh['tglberlaku']]['tglberlaku'] = $rowh['tglberlaku'];
				$a_jamkerja[$rowh['idpegawai']][$rowh['kodekelkerja']][$rowh['tglberlaku']]['keterangan'] = $rowh['keterangan'];
				foreach($a_hari as $hari){
					$a_jamkerja[$rowh['idpegawai']][$rowh['kodekelkerja']][$rowh['tglberlaku']][$hari]['datang'] = $a_jamhadir['jamdatang'][$rowh[$hari]];
					$a_jamkerja[$rowh['idpegawai']][$rowh['kodekelkerja']][$rowh['tglberlaku']][$hari]['pulang'] = $a_jamhadir['jampulang'][$rowh[$hari]];
				}
			}

			$a_data = array('list' => $rs, 'shift' => $a_jamkerja, 'hari' => $a_hari, 'namaunit' => $col['namaunit']);
			
			return $a_data;
		}
		
		/**************************************************** L A P O R A N ******************************************************/
	
	}
?>
