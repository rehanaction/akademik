<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPa extends mModel {
		const schema = 'sdm';
		
		/**************************************************** B O B O T ******************************************************/
		// mendapatkan kueri list untuk setting kehadiran
		function listQueryPeriodeBobot() {
			$sql = "select * from ".static::table('pa_periodebobot');
			
			return $sql;
		}
		
		function listQueryBobot($key) {
			$sql = "select b.*, e.namaeselon from ".static::table('pa_bobot')." b 
					left join ".static::schema()."ms_eselon e on e.kodeeselon=b.kodeeselon
					where kodeperiodebobot='$key'";
			
			return $sql;
		}
		
		function listQueryKategoriNilai($key) {
			$sql = "select * from ".static::table('pa_kategorinilai')." 
					where kodeperiodebobot='$key'";
			
			return $sql;
		}
		
		function listQueryIndeksObj($key) {
			$sql = "select * from ".static::table('pa_indeksnilaiobyektif')." 
					where kodeperiodebobot='$key'";
			
			return $sql;
		}
		
		function listQueryBobotSubj($key) {
			$sql = "select j.namapajenis,b.kodepajenis from ".static::table('pa_bobotnilaisubyektif')." b
					left join ".static::schema()."pa_kategori a on a.kodekategori=b.kodekategori
					left join ".static::schema()."pa_jenispenilai j on j.kodepajenis=b.kodepajenis
					where kodeperiodebobot='$key' group by b.kodepajenis,j.namapajenis";
			
			return $sql;
		}
		
		function listQueryBobotObj($key) {
			$sql = "select b.*,namaeselon from ".static::table('pa_bobotnilaiobyektif')." b
					left join ".static::schema()."ms_eselon e on e.kodeeselon=b.kodeeselon
					where kodeperiodebobot='$key'";
			
			return $sql;
		}
		
		function listQueryForm() {
			$sql = "select j.namapajenis,p.namaperiode,f.* from ".static::table('pa_formsubyektif')." f
					left join ".static::schema()."pa_periodebobot p on p.kodeperiodebobot=f.kodeperiodebobot
					left join ".static::schema()."pa_jenispenilai j on j.kodepajenis=f.kodepajenis";
			
			return $sql;
		}
		
		function getDataEditPeriodeBobot($r_key) {
			$sql = "select * from ".self::table('pa_periodebobot')."  
					where kodeperiodebobot='$r_key'";
			
			return $sql;
		}
		
		function getDataEditOB2($r_key) {
			list($r_periode,$r_pegawai) = explode('|',$r_key);
			$sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					n.kodeperiode,n.nilaiob2,namaperiode,n.idpegawai
					from ".self::table('pa_nilaiakhir')." n
					left join ".static::table('ms_pegawai')." m on m.idpegawai=n.idpegawai
					left join ".static::table('pa_periode')." p on p.kodeperiode=n.kodeperiode
					where n.kodeperiode='$r_periode' and n.idpegawai=$r_pegawai";
			
			return $sql;
		}
		
		function getInfoBobot($conn, $r_key){
			$sql = "select namaperiode from ".static::table('pa_periodebobot')." where kodeperiodebobot='$r_key'";
			
			return $conn->GetRow($sql);
		}
		
		function getLastPeriodeBobot($conn){
			$sql = "select kodeperiodebobot from ".static::table('pa_periodebobot')." order by kodeperiodebobot desc limit 1";
			
			return $conn->GetOne($sql);
		}
		
		function getCEselon($conn){
			$sql = "select kodeeselon, namaeselon from ".static::table('ms_eselon')." order by kodeeselon";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCKategori($conn){
			$sql = "select kodekategori, kategori from ".static::table('pa_kategori')." order by kodekategori";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCKategoriNilai($conn, $r_periode){
			$sql = "select kategorinilai, kategorinilai as label from ".static::table('pa_kategorinilai')." 
					where kodeperiodebobot=(select kodeperiodebobot from ".static::table('pa_periode')." where kodeperiode='$r_periode') order by kategorinilai";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCJenisPenilai($conn){
			$sql = "select kodepajenis, namapajenis from ".static::table('pa_jenispenilai')." order by kodepajenis";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCPeriodeBobot($conn){
			$sql = "select kodeperiodebobot, namaperiode from ".static::table('pa_periodebobot')." order by kodeperiodebobot";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getDataBobotSubj($conn,$key){
			list($r_periode,$r_kodejenis) = explode("|", $key);
			
			$sql = "select a.kategori,j.namapajenis,b.* from ".static::table('pa_bobotnilaisubyektif')." b
					left join ".static::schema()."pa_kategori a on a.kodekategori=b.kodekategori
					left join ".static::schema()."pa_jenispenilai j on j.kodepajenis=b.kodepajenis
					where kodeperiodebobot='$r_periode' and b.kodepajenis='$r_kodejenis'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data['kode'][$row['kodekategori']] = $row['kodekategori'];
				$a_data['nilai'][$row['kodekategori']] = $row['nilai'];
			}
			
			return $a_data;
		}
		
		function getListBobotSubj($conn,$key){			
			$sql = "select a.kategori,j.namapajenis,b.* from ".static::table('pa_bobotnilaisubyektif')." b
					left join ".static::schema()."pa_kategori a on a.kodekategori=b.kodekategori
					left join ".static::schema()."pa_jenispenilai j on j.kodepajenis=b.kodepajenis
					where kodeperiodebobot='$key'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data['kode'][$row['kodekategori']][$row['kodepajenis']] = $row['kodekategori'];
				$a_data['nilai'][$row['kodekategori']][$row['kodepajenis']] = $row['nilai'];
			}
			
			return $a_data;
		}
		
		function getListFormDetail($conn,$key){			
			$sql = "select * from ".static::table('pa_formsubyektifdet')." where kodeformsubyektif='$key' order by infoleft";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getKodeFormDet($conn, $key){
			$sql = "select coalesce(max(cast(nouraian as int)),0)+1 from ".static::table('pa_formsubyektifdet')." where kodeformsubyektif='$key'";
			
			return $conn->GetOne($sql);
		}
		
		function getSoalPA($conn, $r_form, $key){
			$sql = "select nouraian, uraian from ".static::table('pa_formsubyektifdet')." where kodeformsubyektif='$r_form' and nouraian='$key'";
			
			return $conn->GetRow($sql);
		}
		
		function insertRecordSoalPA($conn,$record,$status=false) {
			if($record['parentnouraian'] != 'null') {
				$a_parent = $conn->GetRow("select inforight from ".static::table('pa_formsubyektifdet')." where nouraian = '".$record['parentnouraian']."' and kodeformsubyektif='".$record['kodeformsubyektif']."'");
				$record['level'] = 2;
			}
			else {
				$a_parent = $conn->GetRow("select coalesce(max(inforight),0)+1 as inforight from ".static::table('pa_formsubyektifdet')." where kodeformsubyektif='".$record['kodeformsubyektif']."'");
				$record['level'] = 1;
			}
			
			$record['infoleft'] = $a_parent['inforight'];
			$record['inforight'] = $record['infoleft']+1;
			
			$err = static::insertLeaf($conn,$record['kodeformsubyektif'],$a_parent['inforight']);
			if(!$err) {
				$err = Query::recInsert($conn,$record,static::table('pa_formsubyektifdet'));
				if(!$err) {
					$seq = static::sequence;
					if(empty($seq))
						$key = static::getRecordKey($key,$record);
					else
						$key = static::getLastValue($conn);
				}
			}
			
			if($status)
				return static::insertStatus($conn);
			else
				return $err;
		}
		
		function insertLeaf($conn,$key,$right) {
			$sql = "update ".static::table('pa_formsubyektifdet')." set infoleft = infoleft+2 where kodeformsubyektif='$key' and infoleft > '$right';
					update ".static::table('pa_formsubyektifdet')." set inforight = inforight+2 where kodeformsubyektif='$key' and inforight >= '$right'";
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		function getInfoLeft($conn,$r_subkey){
			list($r_key,$r_keydet) = explode('|',$r_subkey);
			$infoleft = $conn->GetOne("select infoleft from ".static::table('pa_formsubyektifdet')." where kodeformsubyektif='$r_key' and nouraian='$r_keydet'");
			
			return $infoleft;
		}
		
		function deleteLeaf($conn,$key, $left) {
			$sql = "update ".static::table('pa_formsubyektifdet')." set infoleft = infoleft-2 where kodeformsubyektif='$key' and infoleft > '$left';
					update ".static::table('pa_formsubyektifdet')." set inforight = inforight-2 where kodeformsubyektif='$key' and inforight > '$left'";
			$ok = $conn->Execute($sql);
						
			return $conn->ErrorNo();
		}
				
		
		/**************************************************** E N D  O F B O B O T ******************************************************/
		
		function listQueryTim(){
			$sql = "select ".static::schema.".f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,u.namaunit,nip,
					namastatusaktif,n.idpegawai,n.kodeperiode,st.jabatanstruktural, pt.golongan, k.kategori, n.nilaiakhir, n.kategorinilai
					from ".static::table('pa_nilaiakhir')." n
					left join ".static::schema()."ms_pegawai p on p.idpegawai=n.idpegawai
					left join ".static::schema()."lv_statusaktif s on s.idstatusaktif=p.idstatusaktif
					left join ".static::schema()."ms_unit u on u.idunit=n.idunit
					left join ".static::schema()."ms_pangkat pt on pt.idpangkat=p.idpangkat
					left join ".static::schema()."ms_struktural st on st.idjstruktural=p.idjstruktural
					left join ".static::schema()."pa_kategori k on k.kodekategori=n.kodekategori";
			
			return $sql;
		}
		
		function listQueryTimKehadiran(){
			$sql = "select ".static::schema.".f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,u.namaunit,nip,
					namastatusaktif,n.idpegawai,n.kodeperiode,st.jabatanstruktural, pt.golongan, k.kategori, n.nilaiakhir, n.kategorinilai, n.nilaiob2
					from ".static::table('pa_nilaiakhir')." n
					left join ".static::schema()."ms_pegawai p on p.idpegawai=n.idpegawai
					left join ".static::schema()."lv_statusaktif s on s.idstatusaktif=p.idstatusaktif
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_pangkat pt on pt.idpangkat=p.idpangkat
					left join ".static::schema()."ms_struktural st on st.idjstruktural=p.idjstruktural
					left join ".static::schema()."pa_kategori k on k.kodekategori=n.kodekategori";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
					break;
				case 'periodebobot' :
					return "f.kodeperiodebobot='$key'";
					break;
				case 'periode' :
					return "n.kodeperiode='$key'";
					break;
				case 'periodetim' :
					return "kodeperiode='$key'";
					break;
				case 'penilai' :
					return "idpenilai='$key'";
					break;
			}
		}
		
		function listQueryPeriodePA() {
			$sql = "select p.*,b.namaperiode as periodebobot from ".static::table('pa_periode')." p
					left join ".static::schema()."pa_periodebobot b on b.kodeperiodebobot=p.kodeperiodebobot";
			
			return $sql;
		}
		
		function getCPeriode($conn){
			$sql = "select kodeperiode, namaperiode from ".static::table('pa_periode')." order by kodeperiode";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getLastPeriode($conn){
			$sql = "select kodeperiode from ".static::table('pa_periode')." order by kodeperiode desc limit 1";
			
			return $conn->GetOne($sql);
		}
		
		function getCJenisPenilaiKategori($conn, $r_key, $persen=false){
			$sql = "select b.kodepajenis,j.namapajenis from ".static::table('pa_bobotnilaisubyektif')." b 
					left join ".static::schema()."pa_jenispenilai j on j.kodepajenis=b.kodepajenis
					where kodekategori='$r_key' and (nilai is not null or nilai<>0)";
			
			if (!$persen)
				$sql .= " and b.kodepajenis<>'D'";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getKodeFormSubj($conn, $r_periode, $r_jenis){
			$sql = "select kodeformsubyektif from ".static::table('pa_formsubyektif')." 
					where kodeperiodebobot=(select kodeperiodebobot from ".static::schema()."pa_periode where kodeperiode='$r_periode') and kodepajenis='$r_jenis'";
			
			return $conn->GetOne($sql);
		}
		
		/**************************************************** P E N I L A I A N ******************************************************/
		
		function getInfoPenilaian($conn, $r_key){
			list($r_periode, $r_idpegawai) = explode("|", $r_key);
			
			$sql = "select n.*,p.namaperiode, ".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap, u.namaunit 
					from ".static::table('pa_nilaiakhir')." n 
					left join ".static::schema()."pa_periode p on p.kodeperiode=n.kodeperiode
					left join ".static::schema()."ms_pegawai m on m.idpegawai=n.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=m.idunit
					where n.kodeperiode='$r_periode' and n.idpegawai=$r_idpegawai";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		function getListTimPenilai($conn, $r_key){
			list($r_periode, $r_idpegawai) = explode("|", $r_key);
			
			$sql = "select t.*, ".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namapenilai, p.nip, u.namaunit, j.namapajenis  
					from ".static::table('pa_timsubyektif')." t
					left join ".static::schema()."ms_pegawai p on p.idpegawai=t.idpenilai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."pa_jenispenilai j on j.kodepajenis=t.kodepajenis
					where t.kodeperiode='$r_periode' and t.idpegawai=$r_idpegawai order by p.nip,t.kodepajenis";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}
		
		function getDetailPenilaian($conn, $r_key){
			$sql = "select ".static::schema()."f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) as namadinilai,
					".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapenilai,
					d.nip as nipdinilai, p.nip as nippenilai, pr.namaperiode, f.namaform, f.kodeformsubyektif, f.bobotbawah, f.bobotatas, du.namaunit as unitdinilai, pu.namaunit as unitpenilai, t.isselesai
					from ".static::table('pa_timsubyektif')." t 
					left join ".static::schema()."ms_pegawai d on d.idpegawai=t.idpegawai
					left join ".static::schema()."ms_pegawai p on p.idpegawai=t.idpenilai
					left join ".static::schema()."pa_periode pr on pr.kodeperiode=t.kodeperiode
					left join ".static::schema()."ms_unit du on du.idunit=d.idunit
					left join ".static::schema()."ms_unit pu on pu.idunit=p.idunit
					left join ".static::schema()."pa_formsubyektif f on f.kodeformsubyektif=t.kodeformsubyektif
					where t.idtim=$r_key";
			
			return $conn->GetRow($sql);
		}
		
		function listQueryTimPenilai() {
			$sql = "select t.*,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namadinilai, p.nip, u.namaunit,
					pkt.namapangkat as pangkat, pkt.golongan, st.jabatanstruktural, j.namapajenis
					from ".static::table('pa_timsubyektif')." t
					left join ".static::schema()."pa_jenispenilai j on j.kodepajenis=t.kodepajenis
					left join ".static::schema()."ms_pegawai p on p.idpegawai=t.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_pangkat pkt ON pkt.idpangkat=p.idpangkat
					left join ".static::schema()."ms_struktural st ON st.idjstruktural=p.idjstruktural";
			
			return $sql;
		}
		
		function listSoalPenilain($conn, $r_kodeform, $r_key){
			$sql = "select f.*, h.nilai from ".static::table('pa_formsubyektifdet')." f
					left join ".static::schema()."pa_hasilsubyektif h on h.kodeformsubyektif=f.kodeformsubyektif 
					and h.nouraian=f.nouraian and h.idtim=$r_key
					where f.kodeformsubyektif='$r_kodeform' order by infoleft";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function isExistNilai($conn, $r_key){
			$sql = "select nouraian from ".static::table('pa_hasilsubyektif')." where idtim=$r_key";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row['nouraian'];
				
			return $a_data;
		}
		
		function getMonitorPenilai($conn, $r_key){
			
			$sql = "select t.idpegawai, ".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namapenilai, 
					p.nip, t.kodepajenis, t.isselesai, t.idpenilai  
					from ".static::table('pa_timsubyektif')." t
					left join ".static::schema()."ms_pegawai p on p.idpegawai=t.idpenilai
					where t.kodeperiode='$r_key'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_status = array();
			while($row = $rs->FetchRow()){
				$a_data['pegawai'][$row['idpegawai']][$row['kodepajenis']][] = $row['nip'].' - '.$row['namapenilai'];
				$a_data['penilai'][$row['idpegawai']][$row['kodepajenis']][] = $row['idpenilai'];
				$a_status[$row['idpegawai']][$row['idpenilai']] = $row['isselesai'];
			}	
			return array("data" => $a_data, "status" => $a_status);
		}
		
		function hitungKehadiran($conn, $r_periode){
			$sql = "select tglmulaipresensi,tglselesaipresensi 
					from ".static::table('pa_periode')."
					where kodeperiode='$r_periode'";
			$periode = $conn->GetRow($sql);
			
			//komponen kehadiran penambah
			$sql = "select (
					case kodeabsensi 
						when 'T' then sdm.f_diffmenit(cast(cast(jamdatang as int)-minterlambat as varchar),sjampulang) 
						when 'H' then sdm.f_diffmenit(sjamdatang,sjampulang) 
						when 'D' then sdm.f_diffmenit(sjamdatang,sjampulang) 
						when 'HL' then sdm.f_diffmenit(jamdatang,jampulang) 
						when 'S' then sdm.f_diffmenit(sjamdatang,sjampulang)
						when 'I' then sdm.f_diffmenit(sjamdatang,sjampulang)
						when 'A' then sdm.f_diffmenit(sjamdatang,sjampulang) 
					end) as jamhadir, t.idpegawai, t.kodeabsensi
					from  ".static::table('pe_presensidet')."  t
					left join ".static::table('pa_nilaiakhir')." n on n.idpegawai=t.idpegawai
					where kodeperiode='$r_periode' 
					and tglpresensi between '$periode[tglmulaipresensi]' and '$periode[tglselesaipresensi]'
					and kodeabsensi in ('T','H','D','HL','S','I','A')";
			$rs = $conn->Execute($sql);
			$a_data = array();
			$a_pembagi = array();
			while ($row = $rs->FetchRow()){
				if ($row['kodeabsensi'] != 'A')
					$a_data[$row['idpegawai']] += $row['jamhadir'];
					
				$a_pembagi[$row['idpegawai']] += $row['jamhadir'];
			}
			
			$sql = "select sdm.f_diffmenit(sjamdatang,sjampulang) as jamhadir,t.idpegawai
					from ".static::table('pe_presensidet')." t
					left join ".static::table('pe_rwtcuti')." r on r.nourutcuti=t.nourutcuti
					left join ".static::table('pa_nilaiakhir')." n on n.idpegawai=t.idpegawai
					where kodeperiode='$r_periode' and tglpresensi between '$periode[tglmulaipresensi]' and '$periode[tglselesaipresensi]'
					and idjeniscuti<>'CLT' and kodeabsensi='C'";
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data[$row['idpegawai']] += $row['jamhadir'];
				$a_pembagi[$row['idpegawai']] += $row['jamhadir'];
			}
			
			$sql = "select totlembur, t.idpegawai 
					from ".static::table('pe_presensidet')." t
					left join ".static::table('pa_nilaiakhir')." n on n.idpegawai=t.idpegawai
					where kodeperiode='$r_periode' and tglpresensi between '$periode[tglmulaipresensi]' and '$periode[tglselesaipresensi]'
					and totlembur is not null and issetujuatasan='Y'";
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow()){
				$a_data[$row['idpegawai']] += $row['totlembur'];
				$a_pembagi[$row['idpegawai']] += $row['totlembur'];
			}
			
			if (count($a_data) > 0){
				foreach($a_data as $id => $value){
					$record = array();
					$record['nilaiob2'] = round(($value/$a_pembagi[$id])*100,2);
					$r_key = $r_periode.'|'.$id;
					$p_key = "kodeperiode,idpegawai";
					list($p_posterr,$p_postmsg) = mPa::updateRecord($conn,$record,$r_key,true,'pa_nilaiakhir',$p_key);
				}
			}
			
			return $p_postmsg;
		}
		
		function saveFirstTim($conn, $r_periode){
			$conn->StartTrans();
						
			$a_idpegawai = array();
			$sql = "select idpegawai from ".static::table('pa_nilaiakhir')." where kodeperiode='$r_periode'";
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow())
				$a_idpegawai[] = $row['idpegawai']; 
			
			$a_iddirisendiri = array();
			$sql = "select idpegawai from ".static::table('pa_timsubyektif')." where kodeperiode='$r_periode' and kodepajenis='D'";
			$rs = $conn->Execute($sql);
			while ($row = $rs->FetchRow())
				$a_iddirisendiri[] = $row['idpegawai']; 
				
			//seleksi karyawan tetap yang masa kerja lebih dari 1 tahun
			$sql = "select idpegawai,p.idjstruktural,kodeeselon,p.idunit 
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural 
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif=p.idstatusaktif 
					where sdm.get_mkpengabdian(p.idpegawai) >= 1 and p.idhubkerja = 'HT' and a.iskeluar = 'T'";
			$rs = $conn->Execute($sql);
			
			$periodebobot = $conn->GetOne("select kodeperiodebobot from ".static::table('pa_periode')." where kodeperiode='$r_periode'");
			
			$kodeform = $conn->GetOne("select kodeformsubyektif from ".static::table('pa_formsubyektif')." where kodeperiodebobot='$periodebobot' and kodepajenis='D'");
			
			while ($row = $rs->FetchRow()){
				$record = array();
				$record['kodeperiode'] = $r_periode;
				$record['kodeperiodebobot'] = $periodebobot;
				$record['idpegawai'] = $row['idpegawai'];
				$record['idjstruktural'] = CStr::cStrNull($row['idjstruktural']);
				$record['idunit'] = CStr::cStrNull($row['idunit']);
				
				if (empty($row['idjstruktural']))
					$record['kodeeselon'] = 50; //default jika tidak mempunyai jabatan struktural/ non eselon
				else
					$record['kodeeselon'] = $row['kodeeselon'];
				
				if (!in_array($row['idpegawai'], $a_idpegawai))
					mPa::insertRecord($conn, $record, false, 'pa_nilaiakhir');
				else{
					$r_key = $record['kodeperiode'].'|'.$record['idpegawai'];
					$where = 'kodeperiode,idpegawai';
					mPa::updateRecord($conn, $record, $r_key, false, 'pa_nilaiakhir',$where);
				}
				
				$recdetail = array();
				$recdetail['kodeperiode'] = $r_periode;
				$recdetail['idpegawai'] = $row['idpegawai'];
				$recdetail['kodepajenis'] = 'D'; //default pasti diri sendiri ada
				$recdetail['idpenilai'] = $row['idpegawai'];
				$recdetail['kodeformsubyektif'] = $kodeform;
				
				if (!in_array($row['idpegawai'], $a_iddirisendiri))
					mPa::insertRecord($conn, $recdetail, false, 'pa_timsubyektif');
				else{
					$r_key = $recdetail['kodeperiode'].'|'.$recdetail['idpegawai'].'|D';
					$where = 'kodeperiode,idpegawai,kodepajenis';
					mPa::updateRecord($conn, $recdetail, $r_key, false, 'pa_timsubyektif',$where);
				}
			}
			
			$conn->CompleteTrans();
			
			return static::updateStatus($conn);
		}
		
		function deleteTim($conn, $r_key){
			$conn->StartTrans();
			
			list($r_periode, $r_idpegawai) = explode("|", $r_key);
						
			$sql = "delete from ".static::table('pa_hasilsubyektif')." 
					where idtim in (select idtim from ".static::table('pa_timsubyektif')." where 
					kodeperiode='$r_periode' and idpegawai=$r_idpegawai)";
			$conn->Execute($sql);
						
			$sql = "delete from ".static::table('pa_timsubyektif')." where kodeperiode='$r_periode' and idpegawai=$r_idpegawai";
			$conn->Execute($sql);
			
			$sql = "delete from ".static::table('pa_nilaiakhir')." where kodeperiode='$r_periode' and idpegawai=$r_idpegawai";
			$conn->Execute($sql);
			
			$conn->CompleteTrans();
			
			return static::updateStatus($conn);
		}
		
		function hitungPA($conn, $r_periode, $r_pegawai){
			//mendapatkan informasi detail penilaian
			$a_info = $conn->GetRow("select * from ".static::table('pa_nilaiakhir')." where kodeperiode='$r_periode' and idpegawai=$r_pegawai");
			//menghitung subjektif
			$hasilsubj = $conn->GetOne("select sum(nilaisubyektif) from ".static::table('pa_nilaisubyektif')." where kodeperiode='$r_periode' and idpegawai=$r_pegawai");
			
			$sql = "select bobotsubjektif, bobotobjektif from ".static::table('pa_bobot')." 
					where kodeperiodebobot='$a_info[kodeperiodebobot]' and kodeeselon='$a_info[kodeeselon]'";
			$a_bobot = $conn->GetRow($sql);
			
			$sql = "select ob1,ob2 from ".static::table('pa_bobotnilaiobyektif')." 
					where kodeperiodebobot='$a_info[kodeperiodebobot]' and kodeeselon='$a_info[kodeeselon]'";
			$a_bobotobj = $conn->GetRow($sql);
			
			//total subjektif
			$totalsubj = round($hasilsubj * ($a_bobot['bobotsubjektif']/100),2);
			
			//hitung objektif I II
			$totalobj = (($a_info['nilaiob1'] * ($a_bobotobj['ob1']/100)) + ($a_info['nilaiob2'] * ($a_bobotobj['ob2']/100))) * ($a_bobot['bobotobjektif']/100);
			
			$totalnilai = $totalsubj + $totalobj;
			
			$nilaihuruf = $conn->GetOne("select kategorinilai from ".static::table('pa_kategorinilai')." where $totalnilai BETWEEN batasbawah and batasatas");
			
			$record = array();
			$record['kategorinilai'] = $nilaihuruf;
			$record['nilaisubyektif'] = $totalsubj;
			$record['nilaiobyektif'] = $totalobj;
			$record['nilaiakhir'] = $totalnilai;
			$record['isselesai'] = 'Y';
			
			mPa::updateRecord($conn, $record, $r_periode.'|'.$r_pegawai, false, 'pa_nilaiakhir','kodeperiode,idpegawai');
			
			return static::updateStatus($conn);
		}
		
		function getNilaiUnitOB($conn, $r_periode){
			$sql = "select u.idunit, kodeunit, level, namaunit, nilaiob1 from ".static::table('ms_unit')." u 
					left join ".static::schema()."pa_kinerjaob1unit k on k.idunit=u.idunit and k.kodeperiode='$r_periode'
					where (inforight-infoleft!=1) /*(case when isakademik='Y' then else 4 end) or (level=3 and (inforight-infoleft)=1)*/ order by infoleft";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}
		
		function cekHitungNilaiPA($conn, $r_periode){
			$sql = "select idpegawai from ".static::table('pa_nilaiakhir')." 
					where kodeperiode='$r_periode' and idpegawai in (select idpegawai 
					from ".static::table('pa_timsubyektif')." where isselesai<>'Y' and kodeperiode='$r_periode' group by idpegawai)";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['idpegawai']] = $row['idpegawai'];
			
			return $a_data;
		}
		
		/*function isExistNilaiOB($conn, $r_periode){
			$sql = "select idunit,kodeperiode from ".static::table('pa_nilaiob1')." where kodeperiode='$r_periode'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[$row['idunit']] = 
		}*/
				
		/**************************************************** END OF P E N I L A I A N ******************************************************/
		
		/**************************************************** L A P O R A N ******************************************************/
		function getLapPA($conn, $r_periode, $r_unit){
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('pa_periode')." where kodeperiode='$r_periode'");
			
			$col = $conn->GetRow("select namaunit, infoleft, inforight from ".static::table('ms_unit')." where idunit='$r_unit'");
			
			$sql = "select kodepajenis, namapajenis from ".static::table('pa_jenispenilai')." order by namapajenis";
			$a_jenispenilai = Query::arrQuery($conn, $sql);
			
			$sql = "select kodepajenis, nilairerata, nilaisubyektif, n.idpegawai 
					from ".static::table('pa_nilaisubyektif')." n
					left join ".static::table('ms_pegawai')." p on p.idpegawai=n.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit					
					where kodeperiode='$r_periode' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);
			$nilaisuj = array();
			while ($row = $rs->FetchRow()){
				$a_nilaisubj['rata'][$row['idpegawai']][$row['kodepajenis']] = $row['nilairerata'];
				$a_nilaisubj['nilai'][$row['idpegawai']][$row['kodepajenis']] = $row['nilaisubyektif'];
			}	
			
			$sql = "select p.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap,
					s.jabatanstruktural, b.bobotsubjektif, b.bobotobjektif
					from ".static::table('pa_nilaiakhir')." p
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpegawai
					left join ".static::table('ms_struktural')." s on s.idjstruktural=m.idjstruktural
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit
					left join ".static::table('pa_bobot')." b on b.kodeeselon=p.kodeeselon and b.kodeperiodebobot=p.kodeperiodebobot
					where p.kodeperiode='$r_periode' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return array("list" => $a_data, "jenispenilai" => $a_jenispenilai, "unit" => $col['namaunit'], "periode" => $namaperiode, 'nilaisubj' => $a_nilaisubj);
		}
		
		function getRaportPA($conn, $r_periode, $r_pegawai){
			$periode = $conn->GetRow("select namaperiode,kodeperiodebobot from ".static::table('pa_periode')." where kodeperiode='$r_periode'");
			
			
			$sql = "select kodepajenis, namapajenis from ".static::table('pa_jenispenilai')." order by namapajenis";
			$a_jenispenilai = Query::arrQuery($conn, $sql);
						
			$sql = "select p.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap,
					s.jabatanstruktural, u.namaunit, e.namaeselon, k.kategori, p.nilaiob1, p.nilaiob2
					from ".static::table('pa_nilaiakhir')." p
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpegawai
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					left join ".static::table('ms_eselon')." e on e.kodeeselon=p.kodeeselon
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('pa_kategori')." k on k.kodekategori=p.kodekategori
					where p.kodeperiode='$r_periode' and p.idpegawai='$r_pegawai'";
			$data = $conn->GetRow($sql);
			
			$sql = "select kodepajenis, nilai from ".static::table('pa_bobotnilaisubyektif ')." 
					where kodeperiodebobot='$periode[kodeperiodebobot]' and kodekategori='$data[kodekategori]'";
			$a_bobotsubj = Query::arrQuery($conn, $sql);
			
			$sql = "select ob1, ob2 from ".static::table('pa_bobotnilaiobyektif ')." 
					where kodeperiodebobot='$periode[kodeperiodebobot]' and kodeeselon='$data[kodeeselon]'";
			$a_bobotobj = $conn->GetRow($sql);
			
			$sql = "select bobotsubjektif, bobotobjektif from ".static::table('pa_bobot ')." 
					where kodeperiodebobot='$periode[kodeperiodebobot]' and kodeeselon='$data[kodeeselon]'";
			$bobot = $conn->GetRow($sql);
			
			$sql = "select kodepajenis,nilairerata,nilaisubyektif from ".static::table('pa_nilaisubyektif')." where kodeperiode='$r_periode' and idpegawai=$r_pegawai";
			$rs = $conn->Execute($sql);
			$a_nilaisubj = array();
			while($row = $rs->FetchRow()){
				$a_nilaisubj['rata'][(string)$row['kodepajenis']] = (float)$row['nilairerata'];
				$a_nilaisubj['nilai'][(string)$row['kodepajenis']] = (float)$row['nilaisubyektif'];
			}
			
			$a_data = array("bobot" => $bobot, "data" => $data, "jenispenilai" => $a_jenispenilai, "periode" => $periode['namaperiode'], "bobotsubj" => $a_bobotsubj, "bobotobj" => $a_bobotobj, "nilaisubj" => $a_nilaisubj);
							
			return $a_data;
		}
		
		function repStatistikPA($conn,$r_unit,$r_periode){	
			$periode = $conn->GetRow("select namaperiode,kodeperiodebobot from ".static::table('pa_periode')." where kodeperiode='$r_periode'");		
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select pa.kategorinilai,p.idunit from ".static::table('ms_pegawai')." p
					left join ".static::table('pa_nilaiakhir')." pa on pa.idpegawai=p.idpegawai and pa.kodeperiode = '$r_periode'
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);
			
			$a_stskategori = array();
			while($row = $rs->FetchRow())
				if(!empty($row['kategorinilai']))
					$a_stskategori[$row['idunit']][$row['kategorinilai']]++;
			
			$sql = "select kategorinilai, kategorinilai as kategori from ".static::table('pa_kategorinilai')." where kodeperiodebobot = '$r_periode'";
			$rs = $conn->Execute($sql);
			
			$a_kategori = array();
			while ($row = $rs->FetchRow())
				$a_kategori[] = $row;
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'],"periode" => $periode['namaperiode'], "sts" => $a_stskategori, "kategori" => $a_kategori);
			return $a_return;
		}
		
		/**************************************************** E N D OF L A P O R A N ******************************************************/
	
	}
?>
