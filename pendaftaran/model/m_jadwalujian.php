<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJadwalUjian extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_jadwal';
		const sequence = 'pd_jadwal_idjadwal_seq';
		const order = 'tgltes';
		const key = 'idjadwal';
		const label = 'Jadwal Ujian';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select *,extract(week from tgltes) as wk from ".self::table();
			
			return $sql;
		}
		function getListFilter($col,$key) {
			
			switch($col) {
				case 'bulan_mulai': return "extract(MONTH from tgltes) = '$key'";
				case 'minggu_mulai': return "to_char(tgltes,'W') = '$key'";
				case 'tahun_mulai': return "extract(YEAR from tgltes) = '$key'";
			}
		}
		// mendapatkan data
		function getData($conn,$key) {
			if(!empty($key)) {
				$sql = static::dataQuery($key);
				$row = $conn->GetRow($sql);
				
				$row['semester'] = substr($row['periode'],-1);
				$row['tahun'] = substr($row['periode'],0,4);
				
				return $row;
			}
			else
				return array();
		}
		
		function getArrJadwal($conn){
			$data=$conn->GetArray("select p.idjadwal,substr(p.jammulai,1,2)||':'||substr(p.jammulai,3,2) as jammulai,substr(p.jamselesai,1,2)||':'||substr(p.jamselesai,3,2) as jamselesai,r.koderuang,r.lokasi from ".static::table('pd_jadwaldetail')." p 
									join akademik.ms_ruang r 
									on p.koderuang=r.koderuang");
			$arr=array();
			foreach($data as $row)
				$arr[$row['idjadwal']][]=$row['koderuang']."-".$row['lokasi'].", ".$row['jammulai']."-".$row['jamselesai'];
			return $arr;
		}
		// dosen pengajar
		function getDetailJadwal($conn,$key) {
			$sql = "select p.*,r.koderuang||'-'||coalesce(r.lokasi,'')||'-'||coalesce(keterangan,'') as koderuang,k.namakota as kodekota from ".static::table('pd_jadwaldetail')." p 
					join akademik.ms_ruang r on p.koderuang=r.koderuang
					join akademik.ms_kota k on k.kodekota=p.kodekota
					where p.idjadwal='$key'";
			
			return $conn->GetArray($sql);
			
		}
		function Ruang($conn,$kodekota){
			$sql="
				SELECT l.namalokasi as koderuang,r.koderuang||'-'||coalesce(r.lokasi,'') as ruang 
				FROM pendaftaran.lv_lokasiujian l 
				JOIN akademik.ms_ruang r on l.namalokasi=r.koderuang
				where l.kodekota='$kodekota'
				ORDER BY koderuang";
			return $conn->Execute($sql);
		}
		function getRuang($conn){
			$sql="
				SELECT l.namalokasi as koderuang,r.koderuang||'-'||coalesce(r.lokasi,'') as ruang 
				FROM pendaftaran.lv_lokasiujian l 
				JOIN akademik.ms_ruang r on l.namalokasi=r.koderuang
				ORDER BY koderuang";
			return Query::arrQuery($conn,$sql);
		}
		function Kota($conn){
			$sql="
				SELECT kodekota,namakota FROM akademik.ms_kota ORDER BY namakota
				";
			return $conn->Execute($sql);
		}
		function getKota($conn){
			$sql="
				SELECT kodekota,namakota FROM akademik.ms_kota ORDER BY namakota
				";
			return Query::arrQuery($conn,$sql);
		}
		// insert detail
		function insertJadwal($conn,$record) {
			Query::recInsert($conn,$record,static::table('pd_jadwaldetail'));
			
			return static::insertStatus($conn,$kosong,'data jadwal','pd_jadwaldetail');
		}
		// hapus data
		function deleteJadwal($conn,$key) {
			//$cond = static::getCondition($key);
			Query::qDelete($conn,static::table('pd_jadwaldetail')," idjadwaldetail='$key'");
			
			return static::deleteStatus($conn,'data jadwal');
		}
		// hapus data
		function updateJadwal($conn,$record,$key) {
			//$cond = static::getCondition($key);
			Query::recUpdate($conn,$record,static::table('pd_jadwaldetail')," idjadwaldetail='$key'");
			
			return static::updateStatus($conn,'data jadwal');
		}
		function deleteSomeJadwal($conn,$keyjadwal) {
			$peserta = $conn->GetOne("select jumlahpeserta from ".static::table('pd_jadwal')." where idjadwal='$keyjadwal'");
			if(empty($peserta) or $peserta<=0){
				Query::qDelete($conn,static::table('pd_jadwaldetail')," idjadwal='$keyjadwal'");
				return true;
			}else{
				return false;
			}
		}
		function copyJadwalPerbulan($conn,$start,$start_th,$end,$end_th){
			$data=$conn->GetArray("select idjadwal,tgltes,kuota,isaktif from ".static::table('pd_jadwal')." where Extract(month from tgltes)='$start' and Extract(year from tgltes)='$start_th'");
			$conn->BeginTrans();
			$ok=true;
			foreach($data as $row){
				$tgl=date('d',strtotime($row['tgltes']));
				$new_date=date('Y-m-d', mktime(0,0,0,$end,$tgl,$end_th));
				$hari=date('D',strtotime($new_date));
				if($hari!='Sun'){
					$rec=array();
					$rec['tgltes']=$new_date;
					$rec['kuota']=$row['kuota'];
					$rec['isaktif']=$row['isaktif'];
					list($p_posterr,$p_postmsg) = static::insertCRecord($conn,"",$rec,$key_jadwal);
					if($p_posterr) {
						$ok = false;
						break;
					}else if(!$p_posterr){
						$in_detail=$conn->Execute("insert into ".static::table('pd_jadwaldetail')."
													(idjadwal,koderuang,jammulai,jamselesai,kodekota,jalurpenerimaan)
													select $key_jadwal,koderuang,jammulai,jamselesai,kodekota,jalurpenerimaan
													from ".static::table('pd_jadwaldetail')."
													where idjadwal='".$row['idjadwal']."'");
					}
				}
			}
			$conn->CommitTrans($ok);
			return $ok;
			
		}
		function copyJadwalPerminggu($conn,$startweek,$startmonth,$startyear){
			$data=$conn->GetArray("select idjadwal,tgltes,kuota,isaktif from ".static::table('pd_jadwal')." 
						where to_char(tgltes,'W') = '$startweek' 
						and Extract(month from tgltes)='$startmonth'
						and Extract(year from tgltes)='$startyear'
						");
			$conn->BeginTrans();
			$ok=true;
			foreach($data as $row){
				$tgl=$row['tgltes'];
				$new_date=date('Y-m-d', strtotime("$tgl +1 week"));
				$hari=date('D',strtotime($new_date));
				if($hari!='Sun'){
					$rec=array();
					$rec['tgltes']=$new_date;
					$rec['kuota']=$row['kuota'];
					$rec['isaktif']=$row['isaktif'];
					list($p_posterr,$p_postmsg) = static::insertCRecord($conn,"",$rec,$key_jadwal);
					if($p_posterr) {
						$ok = false;
						break;
					}else if(!$p_posterr){
						$in_detail=$conn->Execute("insert into ".static::table('pd_jadwaldetail')."
														(idjadwal,koderuang,jammulai,jamselesai,kodekota,jalurpenerimaan)
														select $key_jadwal,koderuang,jammulai,jamselesai,kodekota,jalurpenerimaan
														from ".static::table('pd_jadwaldetail')."
														where idjadwal='".$row['idjadwal']."'");
					}
				}
			}
			$conn->CommitTrans($ok);
			return $ok;
		}
		
		function getInfoJadwal($conn,$idjadwaldetail){
			$sql = "select p.idjadwal,p.jammulai||'-'||p.jamselesai||' '||coalesce(r.koderuang,'')||'-'||coalesce(r.lokasi,'') as waktuujian,j.tgltes from ".static::table('pd_jadwaldetail')." p 
					join ".static::table('pd_jadwal')." j using (idjadwal)
					left join akademik.ms_ruang r on p.koderuang=r.koderuang
					where p.idjadwaldetail='$idjadwaldetail'";
			return $conn->GetRow($sql);
		}
		
	}
?>
