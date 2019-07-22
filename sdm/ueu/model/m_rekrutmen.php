<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));
	
	class mRekrutmen extends mModel {
		const schema = 'sdm';
		const table = 're_rekrutmen';
		const order = 'tglrekrutmen';
		const key = 'idrekrutmen';
		const label = 'rekrutmen';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select p.*, u.namaunit, j.jenispegawai
					from ".self::table()." p 
					left join ".static::schema()."ms_unit u on u.idunit=p.unitperekrut
					left join ".static::schema()."ms_jenispeg j on j.idjenispegawai=p.jnspegdirekrut";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					//return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
					return '';
			}
		}
		
		// mendapatkan kueri list pelamar
		function listQueryPelamar() {
			$sql = "select * from ".self::table('v_biodatapelamar');
			
			return $sql;
		}
		
		//untuk mendapat
		function getDataEditPelamar($r_key) {
			$sql = "select r.*, 
					case when r.idkelurahan is not null then coalesce(prop.namapropinsi,'') || ', ' || coalesce(kab.namakabupaten,'')||', ' || coalesce(kec.namakecamatan,'') || ', ' || coalesce(kel.namakelurahan,'') end as kelurahan,
					case when r.idkelurahanktp is not null then coalesce(propktp.namapropinsi,'') || ', ' || coalesce(kabktp.namakabupaten,'') || ', ' || coalesce(kecktp.namakecamatan,'') || ', ' || coalesce(kelktp.namakelurahan,'') end  as kelurahanktp,
					substring(masakerjaterakhir::character varying,1,2) as masakerjath, substring(masakerjaterakhir::character varying,3,2) as masakerjabln
					from ".self::table('re_calon')." r
					left join ".self::table('lv_propinsi')." prop on prop.idpropinsi=substring(r.idkelurahan,1,2)
					left join ".self::table('lv_kabupaten')." kab on kab.idkabupaten=substring(r.idkelurahan,1,4)
					left join ".self::table('lv_kecamatan')." kec on kec.idkecamatan=substring(r.idkelurahan,1,6)
					left join ".self::table('lv_kelurahan')." kel on kel.idkelurahan=r.idkelurahan
					left join ".self::table('lv_propinsi')." propktp on propktp.idpropinsi=substring(r.idkelurahanktp,1,2)
					left join ".self::table('lv_kabupaten')." kabktp on kabktp.idkabupaten=substring(r.idkelurahanktp,1,4)
					left join ".self::table('lv_kecamatan')." kecktp on kecktp.idkecamatan=substring(r.idkelurahanktp,1,6)
					left join ".self::table('lv_kelurahan')." kelktp on kelktp.idkelurahan=r.idkelurahanktp
					where  r.nopendaftar='$r_key'";
			
			return $sql;
		}
		
		function getInformasiRE($conn, $r_key) {
			$sql = "select re.*, u.namaunit
					from ".self::table('re_rekrutmen')." re
					left join ".static::schema()."ms_unit u on u.idunit=re.unitperekrut 
					where  idrekrutmen='$r_key'";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		function getJenisRE($conn, $r_key) {
			$sql = "select jenisrekrutmen from ".self::table('re_rekrutmen')." 
					where  idrekrutmen='$r_key'";
					
			return $conn->GetOne($sql);
		}
		
		function getJenjangPend($conn) {
			$sql = "select idpendidikan, namapendidikan from ".self::table('lv_jenjangpendidikan')." 
					order by urutan";
					
			return Query::arrQuery($conn,$sql);
		}
		
		function getJurusan($conn) {
			$sql = "select kodejurusan, namajurusan from ".self::table('ms_jurusan')." 
					order by namajurusan";
					
			return Query::arrQuery($conn,$sql);
		}
		
		function jenispegawaiRE($conn) {
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					order by tipepeg";
			
			return Query::arrQuery($conn,$sql);
		}
		
		
		function getPelamarBaru($conn, $r_key) {
			$sql = "select *
					from ".self::table('re_calon')."
					where  nopendaftar='$r_key'";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		function getKandidatBaru($conn, $r_key) {
			$sql = "select *
					from ".self::table('v_biodatapelamar')."
					where  idrekrutmen='$r_key'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getKandidat($conn, $r_key) {
			$sql = "select r.*,namalengkap,namapendidikan,jeniskelamin,umurth, umurmonth
					from ".self::table('re_kandidat')." r
					left join ".static::schema()."v_biodatapegawai b on b.idpegawai=r.idpegawai
					where  idrekrutmen='$r_key'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function listKandidatBaru($conn, $a_infore){
			$sql = "select *
					from ".self::table('v_biodatapelamar')."
					where idrekrutmen is null 
					and nopendaftar not in (select nopendaftar from ".static::table('re_prosesseleksi')." 
					where idrekrutmen<>$a_infore[idrekrutmen] group by nopendaftar)";
			if (!empty($a_infore['jeniskelamin']))
				$sql .= " and sex='$a_infore[jeniskelamin]'";
			if (!empty($a_infore['syaratusiamin']))
				$sql .= " and cast(SUBSTRING(sdm.get_age(tgllahir),1,2)as integer) >= $a_infore[syaratusiamin]";
			if (!empty($a_infore['syaratusiamax']))
				$sql .= " and cast(SUBSTRING(sdm.get_age(tgllahir),1,2)as integer) <= $a_infore[syaratusiamax]";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function listKandidat($conn, $a_infore){
			$sql = "select v.*, idrekrutmen, rp.kodejurusan, j.namajurusan
					from ".self::table('v_biodatapegawai')." v
					left join ".static::schema()."re_kandidat r on r.idpegawai=v.idpegawai and idrekrutmen='$a_infore[idrekrutmen]'
					left join ".static::table('pe_rwtpendidikan')." rp on rp.idpendidikan=v.idpendidikan and rp.idpegawai=v.idpegawai
					and rp.isvalid='Y' and rp.isdiakuiuniv='Y'
					left join ".static::table('ms_jurusan')." j on j.kodejurusan=rp.kodejurusan
					where  (1=1)";
			if (!empty($a_infore['jeniskelamin']))
				$sql .= " and jeniskelamin='$a_infore[jeniskelamin]'";
			if (!empty($a_infore['syaratusiamin']))
				$sql .= " and cast(SUBSTRING(sdm.get_age(tgllahir),1,2)as int) >= $a_infore[syaratusiamin]";
			if (!empty($a_infore['syaratusiamax']))
				$sql .= " and cast(SUBSTRING(sdm.get_age(tgllahir),1,2)as int) <= $a_infore[syaratusiamax]";
			if (!empty($a_infore['idpendidikan']))
				$sql .= " and v.idpendidikan = '$a_infore[idpendidikan]'";
			if (!empty($a_infore['idjurusan']))
				$sql .= " and rp.kodejurusan = '$a_infore[idjurusan]'";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getPendidikan($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('re_pendpelamar')." where nopendaftar = '$key' order by idpendidikan";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		function getRPendidikan($conn,$key) {
			$sql = "select * from ".static::table('re_pendpelamar')." where nopendaftar = '$key' order by idpendidikan";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getProsesSeleksi($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('re_mekanisme')." where idrekrutmen = '$key' order by urutan";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		function getArrProses($conn,$key) {
			$sql = "select r.idproses,urutan,namaproses from ".static::table('re_mekanisme')." r
					left join ".static::schema()."ms_prosesseleksi p on p.idproses=r.idproses and isaktif='Y'
					where idrekrutmen = '$key' order by urutan";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'pendidikan':
					$info['table'] = 're_pendpelamar';
					$info['key'] = 'nopendpelamar';
					$info['label'] = 'pendidikan pelamar';
					break;
				case 'seleksi':
					$info['table'] = 're_mekanisme';
					$info['key'] = 'idrekrutmen,idproses';
					$info['label'] = 'Proses Seleksi';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}

		function sumKandidat($conn){
			$sql = "select count(idrekrutmen) as jumlah,idrekrutmen 
					from ".static::schema()."re_calon group by idrekrutmen";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[$row['idrekrutmen']] = $row['jumlah'];
			
			return $a_data;
		}
		
		function sumKandidatInt($conn){
			$sql = "select count(idrekrutmen) as jumlah,idrekrutmen 
					from ".static::schema()."re_kandidat group by idrekrutmen";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[$row['idrekrutmen']] = $row['jumlah'];
			
			return $a_data;
		}
		
		function prosesSeleksi($conn){
			$sql = "select idproses, namaproses from ".static::schema()."ms_prosesseleksi where isaktif='Y' order by namaproses";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getProsesSeleksiBaru($conn, $r_key, $r_proses) {
			$sql = "select *
					from ".self::table('v_biodatapelamar')." v
					left join ".static::schema()."re_prosesseleksi p on p.nopendaftar=v.nopendaftar and p.idrekrutmen=v.idrekrutmen 
					where  v.idrekrutmen='$r_key' and p.idproses=$r_proses";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getMekanisme($conn, $r_urutan){
			$sql = "select idproses from ".static::schema()."re_mekanisme where urutan=$r_urutan";
			
			return $conn->GetOne($sql);
		}
		
		function cekKandidatProses($conn, $record){
			$sql = "select 1 from ".static::schema()."re_prosesseleksi where idrekrutmen=$record[idrekrutmen] and idproses=$record[idproses] and nopendaftar='$record[nopendaftar]'";
			
			return $conn->GetOne($sql);
		}
		
		function getLolosSeleksiBaru($conn, $r_key, $r_proses) {
			$sql = "select *
					from ".self::table('v_biodatapelamar')." v
					left join ".static::schema()."re_prosesseleksi p on p.nopendaftar=v.nopendaftar and p.idrekrutmen=v.idrekrutmen 
					where p.statusseleksi='L' and v.idrekrutmen='$r_key' and p.idproses=$r_proses";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
								
		//jenis rekrutmen
		function jenisRekrutmen(){
			return array("P" => "Promosi", "M" => "Mutasi", "B" => "Rekrut Baru");
		}
		
		function getPeriodeRekrut(){
			return '';
		}
		
		function statusLulus(){
			return array("G" => "Gagal", "L" => "Lolos");
		}
		
		function aKetelitian(){
			return array("N" => "Normal", "T" => "Teliti", "TS" => "Teliti Sekali");
		}
		
		function aKecepatan(){
			return array("N" => "Normal", "B" => "Baik", "BS" => "Baik Sekali");
		}
		
		function aKecerdasan(){
			return array("N" => "Normal", "C" => "Cerdas", "CS" => "Cerdas Sekali");
		}
		
		function aSosial(){
			return array("C" => "Cukup pandai bergaul", "L" => "Lancar sekali dalam pergaulan");
		}
		
		function getTipePegawai($conn, $r_jenis){
			$sql = "select idtipepeg from ".static::table('ms_jenispeg')." where idjenispegawai='$r_jenis'";
			
			return $conn->GetOne($sql);
		}
		
		function getGraphData($conn, $r_key) {			
			$a_proses = mRekrutmen::getArrProses($conn, $r_key);
							
			$sql = "select idproses,statusseleksi from ".static::schema()."re_prosesseleksi where idrekrutmen=$r_key";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data[$row['idproses']][$row['statusseleksi']]++;
			}
			
					
			return $a_data;//array('peserta' => $a_glog);
		}
		
		function setNoPendaftar($conn){
			return $conn->GetOne("select max(cast(nopendaftar as int)) from sdm.re_calon");
		}
		
		/******************************************************************************************************************/
		/***************************************************** L A P O R A N **********************************************/
		/******************************************************************************************************************/
		
		function getLapHasilSeleksi($conn, $r_kodeunit, $r_tglmulai, $r_tglselesai, $r_jenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			if ($r_jenis == 'B')
				$sql = "select c.*,u.namaunit,j.jenispegawai, sdm.f_namalengkap(c.gelardepan,c.namadepan,c.namatengah,c.namabelakang,c.gelarbelakang) as namalengkap,
						r.jenisrekrutmen, r.posisikaryawan
						from ".static::table('re_calon')." c
						left join ".static::table('ms_pegawai')." p on p.idpegawai=c.refidpeg
						left join ".static::table('re_rekrutmen')." r on r.idrekrutmen=c.idrekrutmen						
						left join ".static::table('ms_unit')." u on u.idunit=r.unitperekrut
						left join ".static::table('ms_jenispeg')." j on j.idjenispegawai=r.jnspegdirekrut
						where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
						and r.jenisrekrutmen='$r_jenis' and tglditerimapegawai is not null and statuslulus='L'
						and c.tglaktif between '".$r_tglmulai."' and '".$r_tglselesai."'
						order by infoleft";
			else
				$sql = "select sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
						j.jenispegawai, u.namaunit, jenisrekrutmen, r.posisikaryawan
						from ".static::table('re_kandidat')." k
						left join ".static::table('ms_pegawai')." p on p.idpegawai=k.idpegawai
						left join ".static::table('re_rekrutmen')." r on r.idrekrutmen=k.idrekrutmen						
						left join ".static::table('ms_unit')." u on u.idunit=r.unitperekrut
						left join ".static::table('ms_jenispeg')." j on j.idjenispegawai=r.jnspegdirekrut
						where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
						and r.jenisrekrutmen='$r_jenis' and k.issetuju='L' order by infoleft";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			$a_return = array('list' => $a_data, 'namaunit' => $col['namaunit']);
			
			return $a_return;
		}
		
		function getLapProgress($conn, $r_kodeunit, $r_tahun, $r_bulan){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select count(p.idrekrutmen) as jumlah, p.idrekrutmen from ".static::table('re_calon')." p
					left join ".static::schema()."re_rekrutmen r on r.idrekrutmen=p.idrekrutmen
					left join ".static::schema()."ms_unit u on u.idunit=r.unitperekrut
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." and 
					p.statuslulus='L' and p.tglditerimapegawai is not null group by p.idrekrutmen";
			$rs = $conn->Execute($sql);
			
			$a_row = array();
			while ($row = $rs->FetchRow())
				$a_row[$row['idrekrutmen']] = $row['jumlah'];
					
			$sql = "select p.*, u.namaunit, j.jenispegawai
					from ".self::table()." p 
					left join ".static::schema()."ms_unit u on u.idunit=p.unitperekrut
					left join ".static::schema()."ms_jenispeg j on j.idjenispegawai=p.jnspegdirekrut
					where date_part('year',tglrekrutmen) = '$r_tahun' and date_part('month',tglrekrutmen) = '".str_pad($r_bulan, 2, "0", STR_PAD_LEFT)."'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'terima' => $a_row, 'namaunit' => $col['namaunit']);
			
			return $a_data;
		}
		
		function repFormPengajuan($conn, $r_key){
			$sql = "select r.*, u.namaunit, j.namapendidikan 
					from ".static::table('re_rekrutmen')." r
					left join ".static::table('ms_unit')." u on u.idunit=r.unitperekrut
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=r.idpendidikan
					where idrekrutmen=$r_key";
			$a_data = $conn->GetRow($sql);
			
			return $a_data;
		}
		
		function repRekapRekrutmen($conn, $r_unit,$r_tahun,$r_bulan){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select c.nopendaftar,r.unitperekrut,r.jenisrekrutmen 
					from ".static::table('re_calon')." c
					left join ".static::table('re_rekrutmen')." r on r.idrekrutmen=c.idrekrutmen
					left join ".static::table('ms_unit')." u on u.idunit=r.unitperekrut
					where c.statuslulus = 'L' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);
			
			$a_stsrekrutmen = array();
			while($row = $rs->FetchRow())
				$a_stsrekrutmen[$row['unitperekrut']][$row['jenisrekrutmen']]++;
							
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stsrekrutmen);
			return $a_return;
		}
		
		function repRekapSeleksi($conn, $r_unit,$r_tahun,$r_bulan){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select c.nopendaftar,r.unitperekrut,r.jenisrekrutmen 
					from ".static::table('re_calon')." c
					left join ".static::table('re_rekrutmen')." r on r.idrekrutmen=c.idrekrutmen
					left join ".static::table('ms_unit')." u on u.idunit=r.unitperekrut
					where c.statuslulus = 'L' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);
			
			$a_stsrekrutmen = array();
			while($row = $rs->FetchRow())
				$a_stsrekrutmen[$row['unitperekrut']][$row['jenisrekrutmen']]++;
							
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stsrekrutmen);
			return $a_return;
		}
		
		function getLapKandidat($conn, $r_kodeunit, $r_mulai, $r_selesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select c.*,
					r.jenisrekrutmen, r.posisikaryawan
					from ".static::table('v_biodatapelamar')." c
					left join ".static::table('re_rekrutmen')." r on r.idrekrutmen=c.idrekrutmen						
					left join ".static::table('ms_unit')." u on u.idunit=r.unitperekrut
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai=r.jnspegdirekrut
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					and r.tglrekrutmen between '$r_mulai' and '$r_selesai' order by infoleft";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			$a_return = array('list' => $a_data, 'namaunit' => $col['namaunit']);
			
			return $a_return;
		}
		
		function getLapPelamar($conn, $r_mulai, $r_selesai){
			
			$sql = "select c.*
					from ".static::table('v_biodatapelamar')." c					
					where tglterimaberkas between '$r_mulai' and '$r_selesai' order by nopendaftar";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			$a_return = array('list' => $a_data);
			
			return $a_return;
		}
		
		function repDataPelamar($conn,$r_key){
			$sql = "select r.*,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namapelamar,
					case when r.idkelurahan is not null then coalesce(prop.namapropinsi,'') || ', ' || coalesce(kab.namakabupaten,'') || ', ' || coalesce(kec.namakecamatan,'') || ', ' || coalesce(kel.namakelurahan,'') end as kelurahan,
					case when r.idkelurahanktp is not null then coalesce(propktp.namapropinsi,'') || ', ' || coalesce(kabktp.namakabupaten,'') || ', ' || coalesce(kecktp.namakecamatan,'') || ', ' || coalesce(kelktp.namakelurahan,'') end  as kelurahanktp,
					substring(masakerjaterakhir::character varying,1,2) as masakerjath, substring(masakerjaterakhir,3,2) as masakerjabln
					from ".self::table('re_calon')." r
					left join ".self::table('lv_propinsi')." prop on prop.idpropinsi=substring(r.idkelurahan,1,2)
					left join ".self::table('lv_kabupaten')." kab on kab.idkabupaten=substring(r.idkelurahan,1,4)
					left join ".self::table('lv_kecamatan')." kec on kec.idkecamatan=substring(r.idkelurahan,1,6)
					left join ".self::table('lv_kelurahan')." kel on kel.idkelurahan=r.idkelurahan
					left join ".self::table('lv_propinsi')." propktp on propktp.idpropinsi=substring(r.idkelurahanktp,1,2)
					left join ".self::table('lv_kabupaten')." kabktp on kabktp.idkabupaten=substring(r.idkelurahanktp,1,4)
					left join ".self::table('lv_kecamatan')." kecktp on kecktp.idkecamatan=substring(r.idkelurahanktp,1,6)
					left join ".self::table('lv_kelurahan')." kelktp on kelktp.idkelurahan=r.idkelurahanktp
					where  r.nopendaftar='$r_key'";
			
			$row = $conn->GetRow($sql);
			
			//pendidikan
			$sql = "select * from ".static::table('re_pendpelamar')." where nopendaftar = '$r_key' order by idpendidikan";
			$rsp = $conn->Execute($sql);
			
			return array('data' => $row, 'pend' => $rsp);
		}
	}
?>
