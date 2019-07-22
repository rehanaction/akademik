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
		
		function getDataEditPeriodeBobot($r_key) {
			$sql = "select * from ".self::table('pa_periodebobot')."  
					where kodeperiodebobot='$r_key'";
			
			return $sql;
		}
		
		//mendapatkan bobot yang dipake periode
		function getPeriodeBobot($conn, $r_periode){
			$periodebobot = $conn->GetOne("select kodeperiodebobot from ".static::table('pa_periodepa')." where kodeperiodepa = '$r_periode'");
			
			return $periodebobot;
		}
		
		function getInfoBobot($conn, $r_key){
			$sql = "select namaperiode from ".static::table('pa_periodebobot')." where kodeperiodebobot='$r_key'";
			
			return $conn->GetRow($sql);
		}
		
		function getLastPeriodeBobot($conn){
			$sql = "select kodeperiodebobot from ".static::table('pa_periodebobot')." order by kodeperiodebobot desc";
			
			return $conn->GetOne($sql);
		}
		
		function getCPeriodeBobot($conn){
			$sql = "select kodeperiodebobot, namaperiodebobot from ".static::table('pa_periodebobot')." order by kodeperiodebobot";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listQueryPeriodePA() {
			$sql = "select p.*,b.namaperiodebobot as periodebobot,case when p.isaktif='Y' then 'Aktif' when p.isaktif='T' then 'Tidak Aktif' end as statusaktif 
					from ".static::table('pa_periodepa')." p
					left join ".static::schema()."pa_periodebobot b on b.kodeperiodebobot=p.kodeperiodebobot";
			
			return $sql;
		}
		
		function listQuerySettingPA() {
			$sql = "select h.kodeperiodepa,h.idpegawai,pg.nik,".static::schema.".f_namalengkap(pg.gelardepan,pg.namadepan,pg.namatengah,pg.namabelakang,pg.gelarbelakang) as pegawaidinilai 
					from ".static::table('pa_hasilpenilaian')." h
					left join ".static::schema()."ms_pegawai pg on pg.idpegawai=h.idpegawai
					left join ".static::schema()."pa_periodepa p on p.kodeperiodepa=h.kodeperiodepa
					group by h.idpegawai,h.kodeperiodepa,pg.nik,pg.gelardepan,pg.namadepan,pg.namatengah,pg.namabelakang,pg.gelarbelakang";
			
			return $sql;
		}
		
		function getCPeriode($conn){
			$sql = "select kodeperiodepa, namaperiodepa from ".static::table('pa_periodepa')." order by kodeperiodepa";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getLastPeriode($conn){
			$sql = "select kodeperiodepa from ".static::table('pa_periodepa')." order by kodeperiodepa desc";
			
			return $conn->GetOne($sql);
		}
		
		function getDataPeriodePA($r_key){			
			$sql = "select p.* from ".static::table('pa_periodepa')." p
					where p.kodeperiodepa = '$r_key' ";
			
			return $sql;
		}
		
		function getDataSettingPA($kodeperiodepa,$idpegawai){			
			$sql = "select kodeperiodepa,h.idpegawai,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as pegawaidinilai
					from ".static::table('pa_hasilpenilaian')." h
					left join ".static::table('ms_pegawai')." p on p.idpegawai=h.idpegawai
					where h.kodeperiodepa = '$kodeperiodepa' and h.idpegawai=$idpegawai
					group by kodeperiodepa,h.idpegawai,p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang";
			
			return $sql;
		}
		
		function cekAktifPeriodePa($conn,$key){
			$cekAktif = $conn->GetOne("select 1 from ".static::table('pa_periodepa')." where isaktif='Y' and kodeperiodepa <> '$key'");
			
			return $cekAktif;
		}
		
		//skala penilaian
		function getCSkala($conn, $r_periodebobot){
			$sql = "select kodeskala, nilaihuruf from ".static::table('pa_skala')." where kodeperiodebobot = '$r_periodebobot' order by kodeskala";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listQuerySkala() {
			$sql = "select pb.* from ".static::table('pa_skala')." pb";
			
			return $sql;
		}
		
		function getDataSkala($r_key){			
			$sql = "select p.* from ".static::table('pa_skala')." p
					where p.kodeskala = '$r_key' ";
			
			return $sql;
		}
		
		//cek skala nilai inputan
		function cekSkala($conn,$r_nilaibawah,$r_nilaiatas){
			
			if($r_nilaiatas < $r_nilaibawah)
				$hasil = 1;
				
			return $hasil;
		}
		
		//cek nilai terbawah
		function cekNilaiTerbawah($conn,$r_kodeperiodebobot,$r_nilaibawah){
			$isNilaiTerbawah = $conn->GetOne("select count(kodesoal) from  ".static::table('pa_soal')." where kodeperiodebobot = '$r_kodeperiodebobot'");
			
			if($r_nilaibawah < (1*$isNilaiTerbawah))
				$hasil = 1;
				
			return $hasil;
		}
		
		//cek nilai terbawah
		function cekNilaiTeratas($conn,$r_kodeperiodebobot,$r_nilaiatas){
			$sql = "select p.jmlskala,count(s.kodesoal) as jmlsoal from  ".static::table('pa_periodebobot')." p
					left join ".static::table('pa_soal')." s on s.kodeperiodebobot = p.kodeperiodebobot 
					where p.kodeperiodebobot = '$r_kodeperiodebobot' group by p.kodeperiodebobot";
			
			$isNilaiTeratas = $conn->GetRow($sql);
			
			$maxNilaiAtas = $isNilaiTeratas['jmlskala']*$isNilaiTeratas['jmlsoal'];
			
			if($r_nilaiatas > $maxNilaiAtas)
				$hasil = 1;
				
			return $hasil;
		}
		
		function listQuerySoal() {
			$sql = "select pb.* from ".static::table('pa_soal')." pb";
			
			return $sql;
		}
		
		function getDataSoal($r_key){
			
			$sql = "select p.* from ".static::table('pa_soal')." p
					where p.kodesoal = '$r_key' ";
			
			return $sql;
		}				
		
		/**************************************************** E N D  O F B O B O T ******************************************************/
		
		//menyimpan penilai
		function savePenilai($conn,$r_key,$r_pegawai){
			$isExist = $conn->GetOne("select 1 from  ".static::table('pa_penilai')." where kodeperiodepa = '$r_key' and idpenilai = '$r_pegawai'");
			
			$record = array();
			$record['kodeperiodepa'] = $r_key;
			$record['idpenilai'] = $r_pegawai;
			
			if(empty($isExist))
				list($p_posterr,$p_postmsg) = self::insertRecord($conn,$record,true,'pa_penilai');
			else
				list($p_posterr,$p_postmsg) = self::updateRecord($conn,$record,$r_key.'|'.$r_pegawai,true,'pa_penilai','kodeperiodepa,idpenilai');
				
			return array($p_posterr,$p_postmsg);
		}
		
		//menyimpan pegawai yang dinilai
		function savePegawaiDet($conn,$r_key,$r_pegawai){
			list($periode,$idpenilai) = explode("|",$r_key);
			
			$isSudahDinilai = $conn->GetOne("select 1 from  ".static::table('pa_hasilpenilaian')." where kodeperiodepa = '$periode' and idpenilai <> '$idpenilai' and idpegawai = '$r_pegawai'");
			$isExist = $conn->GetOne("select 1 from  ".static::table('pa_hasilpenilaian')." where kodeperiodepa = '$periode' and idpenilai= '$idpenilai' and idpegawai = '$r_pegawai'");
			
			$record = array();
			$record['kodeperiodepa'] = $periode;
			$record['idpenilai'] = $idpenilai;
			$record['idpegawai'] = $r_pegawai;
			
			if(empty($isSudahDinilai)){
				if(empty($isExist))
					list($p_posterr,$p_postmsg) = self::insertRecord($conn,$record,true,'pa_hasilpenilaian');
				else
					list($p_posterr,$p_postmsg) = self::updateRecord($conn,$record,$r_key.'|'.$r_pegawai,true,'pa_hasilpenilaian','kodeperiodepa,idpenilai,idpegawai');
			} else{
					list($p_posterr,$p_postmsg) = array(true,"Pegawai telah dinilai penilai lain");
			}
			return array($p_posterr,$p_postmsg);
		}
		
		//list pegawai dinilai
		function listPAPegawaiDinilai($r_key){
			list($periode,$idpegawai) = explode("|",$r_key);
			
			$sql = "select b.*,p.nik||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					namaunit
					from ".static::table('pa_hasilpenilaian')." b
					left join ".static::table('ms_pegawai')." p on p.idpegawai = b.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					where b.kodeperiodepa = '$periode' and b.idpenilai = '$idpegawai'";
			
			return $sql;
		}
		
		//info penilai
		function getInfoPenilai($conn,$r_key){
			list($periode,$idpegawai) = explode("|",$r_key);
			
			$sql = "select b.*,p.nik||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					pa.namaperiodepa, s.jabatanstruktural, u.namaunit from ".static::table('pa_penilai')." b
					left join ".static::table('pa_periodepa')." pa on pa.kodeperiodepa = b.kodeperiodepa
					left join ".static::table('ms_pegawai')." p on p.idpegawai = b.idpenilai
					left join ".static::table('ms_struktural')." s on s.idjstruktural = p.idjstruktural
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					where b.kodeperiodepa = '$periode' and b.idpenilai = '$idpegawai'";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		function listQueryPenilai(){
			$sql = "select idpenilai,".static::schema.".f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as penilai,nik
					from ".static::table('pa_penilai')." n
					left join ".static::schema()."ms_pegawai p on p.idpegawai=n.idpenilai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit";
			
			return $sql;
		}
		
		function getDetailDinilai($conn,$r_periodepa){
			$sql = "select idpenilai, nik||' - '||".static::schema.".f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as dinilai,
					nilaiakhir
					from ".static::table('pa_hasilpenilaian')." n
					left join ".static::schema()."ms_pegawai p on p.idpegawai=n.idpegawai
					where kodeperiodepa='$r_periodepa'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[][$row['idpenilai']]['dinilai'] = $row['dinilai'];
				$a_data[][$row['idpenilai']]['nilaiakhir'] = $row['nilaiakhir'];
			
			return $a_data;
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
					return "pb.kodeperiodebobot='$key'";
					break;
				case 'periode' :
					return "n.kodeperiode='$key'";
					break;
				case 'periodetim' :
					return "p.kodeperiodepa='$key'";
					break;
				case 'penilai' :
					return "idpenilai='$key'";
					break;
				case 'status' :
					if($key == 'T')
						return "isselesai is null";
					else if($key != 'all')
						return "isselesai='$key'";
					else
						return "(1=1)";
					break;
			}
		}
		//jumlah jenis penilai
		function jumJenisPenilai($conn){
			$jumlah = $conn->GetOne("select count(kodepajenis) from ".static::table('pa_jenispenilai')."");
			
			return $jumlah;
		}
		
		//mendapatkan jenis penilai
		function jenisPenilai($conn){
			$sql = "select kodepajenis,namapajenis from ".static::table('pa_jenispenilai')."";
			
			return Query::arrQuery($conn, $sql);
		}
		
		//mendapatkan penilai
		function getTimPenilai($conn,$periodepa){
			$sql = "select h.idpegawai,idpenilai,pg.nik,".static::schema.".f_namalengkap(pg.gelardepan,pg.namadepan,pg.namatengah,pg.namabelakang,pg.gelarbelakang) as penilai,h.kodepajenis,namapajenis 
					from ".static::table('pa_hasilpenilaian')." h
					left join ".static::schema()."pa_jenispenilai j on j.kodepajenis=h.kodepajenis
					left join ".static::schema()."ms_pegawai pg on pg.idpegawai=h.idpenilai
					where h.kodeperiodepa='$periodepa'
					group by h.idpegawai,idpenilai,pg.nik,pg.gelardepan,pg.namadepan,pg.namatengah,pg.namabelakang,pg.gelarbelakang,h.kodepajenis,namapajenis order by h.kodepajenis";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['idpegawai']][$row['nik']] = $row;
			}
			
			return $a_data;
		}

		function listTimPenilai($conn,$periodepa){
			$sql = "select h.idpegawai,idpenilai,pg.nik,".static::schema.".f_namalengkap(pg.gelardepan,pg.namadepan,pg.namatengah,pg.namabelakang,pg.gelarbelakang) as penilai,h.kodepajenis,namapajenis 
					from ".static::table('pa_hasilpenilaian')." h
					left join ".static::schema()."pa_jenispenilai j on j.kodepajenis=h.kodepajenis
					left join ".static::schema()."ms_pegawai pg on pg.idpegawai=h.idpenilai
					where h.kodeperiodepa='$periodepa' order by h.kodepajenis";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['idpegawai']][$row['kodepajenis']][] = $row;
			}
			
			return $a_data;
		}
		
		/**************************************************** P E N I L A I A N ******************************************************/
		
		function getNamaPenilai($conn, $key){
			$sql = "select sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap from ".static::schema.".pa_penilai n
					left join ".static::schema.".ms_pegawai p on p.idpenilai = n.idpegawai
					n.where idpegawai='$key'";
			
			return $conn->GetOne($sql);
		}
		
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
		
		function getCPenilai($conn,$kodeperiodepa){
			$sql = "select idpenilai, ".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap 
					from ".static::table('pa_hasilpenilaian')." n
					left join ".static::table('ms_pegawai')." p on p.idpegawai=n.idpenilai
					where n.kodeperiodepa='$kodeperiodepa'
					order by idpenilai";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Penilai --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['idpenilai']] = $row['namalengkap'];
			}
			
			return $a_data;
		}
		
		function getDetailPenilaian($conn,$r_key){
			list($kodeperiodepa,$idpenilai,$idpegawai) = explode('|',$r_key);
			
			$sql = "select d.idpegawai,".static::schema()."f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) as namadinilai,
					".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapenilai,nilaiakhir,isselesai,h.tglpenilaian,
					sp.jabatanstruktural as jabatandinilai, sn.jabatanstruktural as jabatanpenilai,du.namaunit as unitdinilai,pu.namaunit as unitpenilai,pr.kodeperiodebobot,pr.kodeperiodepa,pr.namaperiodepa
					from ".static::table('pa_hasilpenilaian')." h 
					left join ".static::schema()."ms_pegawai d on d.idpegawai=h.idpegawai
					left join ".static::schema()."ms_pegawai p on p.idpegawai=h.idpenilai
					left join ".static::schema()."ms_struktural sp on sp.idjstruktural=d.idjstruktural
					left join ".static::schema()."ms_struktural sn on sn.idjstruktural=p.idjstruktural
					left join ".static::schema()."pa_periodepa pr on pr.kodeperiodepa=h.kodeperiodepa
					left join ".static::schema()."ms_unit du on du.idunit=d.idunit
					left join ".static::schema()."ms_unit pu on pu.idunit=p.idunit
					where h.kodeperiodepa = '$kodeperiodepa' and h.idpenilai=$idpenilai and h.idpegawai=$idpegawai";
			$a_data = $conn->GetRow($sql);
			
			return $a_data;		
		}
		
		function listQueryTimPenilai() {
			$sql = "select p.*,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namadinilai, pg.nik, u.namaunit,
					pd.namapendidikan, st.jabatanstruktural
					from ".static::table('pa_hasilpenilaian')." p
					left join ".static::schema()."ms_pegawai pg on pg.idpegawai=p.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=pg.idunit
					left join ".static::schema()."lv_jenjangpendidikan pd ON pd.idpendidikan=pg.idpendidikan
					left join ".static::schema()."ms_struktural st ON st.idjstruktural=pg.idjstruktural";
			
			return $sql;
		}
		
		function listSoalPenilaian($conn, $r_kodeperiodebobot){
			$sql = "select s.*,p.idpegawai,p.nilai from ".static::table('pa_soal')." s
					left join ".static::table('pa_penilaian')." p on p.kodesoal=s.kodesoal
					where s.kodeperiodebobot='$r_kodeperiodebobot' order by p.idpegawai,s.urutan";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
					$a_data[$row['idpegawai']][] = $row;
			
			return $a_data;
		}
		
		function getSoalPenilaian($conn, $r_kodeperiodebobot,$idpegawai){
			$sql = "select s.*,p.idpegawai,p.nilai from ".static::table('pa_soal')." s
					left join ".static::table('pa_penilaian')." p on p.kodesoal=s.kodesoal and idpegawai=$idpegawai
					where s.kodeperiodebobot='$r_kodeperiodebobot' order by p.idpegawai,s.urutan";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
					$a_data[] = $row;
			
			return $a_data;
		}
		
		//mendapatkan skala penilaian
		function getSkalaPenilaian($conn, $kodeperiodebobot){
			//list skala penilaian
			$sql = "select * from ".static::table('pa_skala')." where kodeperiodebobot='$kodeperiodebobot'
					order by kodeskala";
			$rss = $conn->Execute($sql);
			
			$a_skala = array();
			while($row = $rss->FetchRow())
				$a_skala[] = $row;
			
			
			
			return $a_skala;
		}
		
		function getSkalaSoal($conn, $r_kodeperiodebobot){
			$jmlskala = $conn->GetOne("select jmlskala from ".static::table('pa_periodebobot')." where kodeperiodebobot='$r_kodeperiodebobot'");
			
			return $jmlskala;
		}
		
		function saveJawaban($conn,$record,$r_key,$table){
			list($r_periodepa,$r_idpenilai,$r_idpegawai) = explode("|", $r_key);
			$key = $r_periodepa .'|'.$r_idpegawai;
			$p_key = 'kodeperiodepa,idpegawai';
			
			list($err,$msg) = self::delete($conn,$key,'pa_penilaian',$p_key);
			
			if(!$err){
				$err = self::insertRecord($conn,$record,false,'pa_penilaian');
			}
			
			if($err)
				$msg = 'Penyimpanan jawaban gagal';
			else
				$msg = 'Penyimpanan jawaban berhasil';
				
			return array($err,$msg);
		}
		
		function getCStatus(){	
			$a_data = array();
			$a_data = array('all' => '-- Semua Status --', 'Y' => 'Selesai', 'T' => 'Belum Selesai');
			
			return $a_data;
		}
		
		function Detailsoal($conn, $r_key){
			list($kodeperiodepa,$idpenilai,$idpegawai) = explode('|',$r_key);
			
			$sql = "select kodesoal,nilai from ".static::table('pa_penilaian')."
					where kodeperiodepa ='$kodeperiodepa' and idpegawai = $idpegawai order by kodesoal";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function isExistNilai($conn){
			$sql = "select kodesoal from ".static::table('pa_soal')."";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row['kodesoal'];
				
			return $a_data;
		}
		
		function getInfoNilai($conn,$r_periode,$r_idpenilai){
			
			$sql = "select h.idpegawai,h.nilaiakhir,p.kodeperiodebobot from ".static::table('pa_hasilpenilaian')." h
					left join ".static::table('pa_periodepa')." p on p.kodeperiodepa = h.kodeperiodepa
					where h.kodeperiodepa='$r_periode' and idpenilai=$r_idpenilai ";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data[$row['idpegawai']] = $row['nilaiakhir'];
				$a_data['kodeperiodebobot'] = $row['kodeperiodebobot'];
			}
			
			print_r($a_data);
		}
		
		/*****************************SALIN ASPEK DAN SKALA DARI PERIODE LAIN**************************/
		
		function getCSalinPeriode($conn,$r_periodebobot){	
			$sql = "select kodeperiodebobot, namaperiodebobot from ".static::table('pa_periodebobot')."
					where kodeperiodebobot <>'$r_periodebobot'
					order by kodeperiodebobot";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodeperiodebobot']] = $row['namaperiodebobot'];
			}
			
			return $a_data;
		}
		
		function saveSalinAspek($conn,$r_periodebobot,$p_dbtable,$f_key){
			$err = 0;
			list($err,$msg) = self::delete($conn,$r_periodebobot,'pa_soal','kodeperiodebobot');
			
			if(!$err){
				$sql = "insert into ".static::table('pa_soal')." 
						select kodesoal,'$r_periodebobot',namasoal,urutan,'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('pa_soal')."
						where kodeperiodebobot = '$f_key'";
				
				$conn->Execute($sql);
			}
			if($err)
				$msg = 'Salin aspek penilaian gagal, data masih digunakan';
			else
				$msg = 'Salin aspek penilaian berhasil';
			
			return array($err,$msg);
		}
		
		function saveSalinSkala($conn,$r_periodebobot,$p_dbtable,$f_key){
			$err = 0;
			list($err,$msg) = self::delete($conn,$r_periodebobot,'pa_skala','kodeperiodebobot');
			
			if(!$err){
				$sql = "insert into ".static::table('pa_skala')." 
						select kodeskala,'$r_periodebobot',nilaibawah,nilaiatas,nilaihuruf,predikat,'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('pa_skala')."
						where kodeperiodebobot = '$f_key'";
				
				$conn->Execute($sql);
			}
			if($err)
				$msg = 'Salin skala penilaian gagal, data masih digunakan';
			else
				$msg = 'Salin skala penilaian berhasil';
			
			return array($err,$msg);
		}
		
		//cek aspek dan skala penilaian
		function getInfoCekSkala($conn, $kodeperiodebobot){
			$sql = "select min(nilaibawah) as minnilaibawah, max(nilaiatas) as maxnilaiatas from ".static::table('pa_skala')." 
					where kodeperiodebobot='$kodeperiodebobot'";
			$rs = $conn->Execute($sql);
			
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data['nilaibawah'] = $row['minnilaibawah'];
				$a_data['nilaiatas'] = $row['maxnilaiatas'];
			}
			
			return $a_data;
		}
		
		/**************************************************** END OF P E N I L A I A N ******************************************************/
		
		/**************************************************** L A P O R A N ******************************************************/
		
		//list query laporan rekpaitulasi penilaian
		function listQueryRekapPenilaian($conn,$kodeperiodepa,$idpenilai){
			$sql = "select h.idpenilai,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapenilai,
					pu.namaunit as unitpenilai,pr.namaperiodepa
					from ".static::table('pa_hasilpenilaian')." h 
					left join ".static::schema()."ms_pegawai p on p.idpegawai=h.idpenilai
					left join ".static::schema()."pa_periodepa pr on pr.kodeperiodepa=h.kodeperiodepa
					left join ".static::schema()."ms_unit pu on pu.idunit=p.idunit
					where h.kodeperiodepa = '$kodeperiodepa'";
			
			if(!empty($idpenilai)){
				if($idpenilai !='all')
					$sql .= " and h.idpenilai = $idpenilai";
				else
					(1==1);
			}
			
			$rs = $conn->Execute($sql);
			
			//mencari kodeperiode bobot dari kodeperiode pa
			$kodeperiodebobot = $conn->GetOne("select kodeperiodebobot from ".static::table('pa_periodepa')." where kodeperiodepa='$kodeperiodepa'");
			
			$a_data = array('list' => $rs,'kodeperiodebobot' => $kodeperiodebobot);
			
			return $a_data;		
		}
		
		function listDetailRekapPenilaian($conn, $kodeperiodepa){
			$sql = "select h.idpenilai,h.nilaiakhir,h.tglpenilaian,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namadinilai,
					u.namaunit as unit
					from ".static::table('pa_hasilpenilaian')." h
					left join ".static::table('ms_pegawai')." p on p.idpegawai=h.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where h.kodeperiodepa='$kodeperiodepa' and h.isselesai='Y' order by h.idpenilai";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['idpenilai']][] = $row;
			
			return $a_data;
		}
		
		//list query laporan hasil penilaian
		function repHasilPenilaian($conn,$kodeperiodepa,$idpenilai,$idpegawai){
			$sql = "select d.idpegawai,".static::schema()."f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) as namadinilai,
					".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapenilai,nilaiakhir,isselesai,h.tglpenilaian,
					sp.jabatanstruktural as jabatandinilai, sn.jabatanstruktural as jabatanpenilai,du.namaunit as unitdinilai,pu.namaunit as unitpenilai,pr.kodeperiodebobot,pr.kodeperiodepa
					from ".static::table('pa_hasilpenilaian')." h 
					left join ".static::schema()."ms_pegawai d on d.idpegawai=h.idpegawai
					left join ".static::schema()."ms_pegawai p on p.idpegawai=h.idpenilai
					left join ".static::schema()."ms_struktural sp on sp.idjstruktural=d.idjstruktural
					left join ".static::schema()."ms_struktural sn on sn.idjstruktural=p.idjstruktural
					left join ".static::schema()."pa_periodepa pr on pr.kodeperiodepa=h.kodeperiodepa
					left join ".static::schema()."ms_unit du on du.idunit=d.idunit
					left join ".static::schema()."ms_unit pu on pu.idunit=p.idunit
					where h.kodeperiodepa = '$kodeperiodepa' and h.isselesai='Y'";
			
			if(!empty($idpenilai)){
				if($idpenilai !='all')
					$sql .= " and h.idpenilai = $idpenilai";
				else
					(1==1);
			}
			if(!empty($idpegawai)){
				$sql .= " and h.idpegawai=$idpegawai";	
			}
			
			$rs = $conn->Execute($sql);
			
			//mencari kodeperiode bobot dari kodeperiode pa
			$kodeperiodebobot = $conn->GetOne("select kodeperiodebobot from ".static::table('pa_periodepa')." where kodeperiodepa='$kodeperiodepa'");
			
			//mencari jumlahskala
			$jmlskala = $conn->GetOne("select jmlskala from ".static::table('pa_periodebobot')." where kodeperiodebobot='$kodeperiodebobot'");
		
			
			$a_data = array('list' => $rs, 'jmlskala' => $jmlskala,'kodeperiodebobot' => $kodeperiodebobot);
			
			return $a_data;
		}
		
		/**************************************************** E N D OF L A P O R A N ******************************************************/
		
	}
?>
