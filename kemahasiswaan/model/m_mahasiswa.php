<?php
	// model mahasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('biodata'));

	class mMahasiswa extends mBiodata {
		const schema = 'akademik';
		const table = 'ms_mahasiswa';
		const order = 'nim';
		const key = 'nim';
		const label = 'mahasiswa';

		const rfidLength = 10;

		// mendapatkan kueri list
		function listQuery() {
			// $sql = "select * from v_mhslist";
			$sql = "select m.sistemkuliah,m.nim, m.nama, m.sex, m.tgllahir, m.semestermhs, m.skslulus, m.ipk, m.statusmhs, m.alamat, m.telp, m.hp, m.email,
					m.kodeunit, u.namaunit, up.namaunit as namafakultas from ".static::table()." m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join gate.ms_unit up on u.kodeunitparent = up.kodeunit
					left join pendaftaran.pd_pendaftar p on p.nimpendaftar=m.nim";

			return $sql;
		}

		// mendapatkan data pager
		function getPagerData($conn,$kolom,$row,&$page,&$sort,$filter='',$sql='') {
			/*if(is_array($filter)) {
				$t_cek = '(m.kodeunit in (';
				$t_len = strlen($t_cek);

				foreach($filter as $t_idx => $t_filter) {
					if(substr($t_filter,0,$t_len) == $t_cek) {
						$t_in = substr($t_filter,$t_len,strlen($t_filter)-$t_len-2);

						$t_sql = "select distinct c.kodeunit from gate.ms_unit p
								left join gate.ms_unit c on c.infoleft >= p.infoleft and c.inforight <= p.inforight
								where p.kodeunit in ($t_in)";
						$rs = $conn->Execute($t_sql);

						$t_unit = array();
						while($t_row = $rs->FetchRow())
							$t_unit[] = $t_row['kodeunit'];

						$filter[$t_idx] = "(m.kodeunit in ('".implode("','",$t_unit)."'))";
					}
				}
			}*/

			return parent::getPagerData($conn,$kolom,$row,$page,$sort,$filter,$sql);
		}

		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data['angkatan'] = 'substring(m.periodemasuk,1,4)';
			$data['pembimbing'] = 'm.nipdosenwali';
			$data['statusmhs'] = 'm.statusmhs';
			$data['unit'] = 'm.kodeunit';

			return $data;
		}

		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));

					$row = mUnit::getData($conn,$key);

					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				default:
					return parent::getListFilter($col,$key);
			}
		}

		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select m.*, u.kodeunitparent as kodefakultas, substring(m.periodemasuk,1,4) as tahundaftar, substring(m.periodemasuk,5,1) as semesterdaftar,
					k.kodepropinsi, ko.namakota as namakotaortu, pko.namapropinsi as namapropinsiortu, ko.kodepropinsi as kodepropinsiortu, kp.kodepropinsi as kodepropinsiperusahaan,
					kn.kodepropinsi as kodepropinsiponpes, ks.kodepropinsi as kodepropinsismu, kt.kodepropinsi as kodepropinsipt
					from ".static::table()." m
					left join gate.ms_unit u on u.kodeunit = m.kodeunit
					left join ".static::table('ms_kota')." k on k.kodekota = m.kodekota
					left join ".static::table('ms_kota')." ko on ko.kodekota = m.kodekotaortu
					left join ".static::table('ms_propinsi')." pko on pko.kodepropinsi = ko.kodepropinsi
					left join ".static::table('ms_kota')." kp on kp.kodekota = m.kodekotaperusahaan
					left join ".static::table('ms_kota')." kn on kn.kodekota = m.kodekotaponpes
					left join ".static::table('ms_kota')." ks on ks.kodekota = m.kodekotasmu
					left join ".static::table('ms_kota')." kt on kt.kodekota = m.kodekotapt
					where ".static::getCondition($key);

			return $sql;
		}

		// mendapatkan info mahasiswa
		function getDataSingkat($conn,$key='') {
			if(empty($key))
				$key = Modul::getUserName();
				
			$sql = "select m.periodemasuk,m.nim,m.nimlama, m.nama,m.sistemkuliah,
					st.namasistem||'-'||st.tipeprogram as namasistemkuliah, m.periodemasuk,
					m.thnkurikulum, m.sex, m.semestermhs, m.ipk, m.skslulus, m.ipslalu,
					m.statusmhs,p.nidn,
					p.idpegawai::text as nipdosenwali, m.kodeunit, m.batassks,  s.namastatus,
					akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as dosenwali,
					up.namaunit as fakultas, u.namaunit as jurusan, m.biodataterisi,m.mhstransfer,m.ptjurusan,m.rfid
					from ".static::table()." m left join ".static::table('lv_statusmhs')." s on m.statusmhs = s.statusmhs
					left join sdm.ms_pegawai p on p.idpegawai::text = m.nipdosenwali
					left join gate.ms_unit u on u.kodeunit = m.kodeunit
					left join gate.ms_unit up on u.kodeunitparent = up.kodeunit
					left join akademik.ak_sistem st on m.sistemkuliah=st.sistemkuliah
					where ".static::getCondition($key);
			$row = $conn->GetRow($sql);

			// ambil data tambahan
			$a_sex = static::jenisKelamin();
			$row['namasex'] = $row['sex'].' ('.$a_sex[$row['sex']].')';
			$row['namaperiodedaftar'] = Akademik::getNamaPeriode($row['periodemasuk']);
			//$row['kurikulum'] = static::getKurikulum($conn,$row['periodemasuk'],$row['kodeunit']);
			//$row['kurikulum']=substr($row['periodemasuk'],0,4);
			//if($row['kurikulum']>=2013)
				//$row['kurikulum']=$conn->GetOne("select thnkurikulumsekarang from akademik.ms_setting order by thnkurikulumsekarang desc limit 1");

			$row['kurikulum']=Akademik::getKurikulum();
			return $row;
		}

		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'beasiswa':
					$info['table'] = 'ak_penerimabeasiswa';
					$info['key'] = 'idbeasiswa,nim';
					$info['label'] = 'beasiswa';
					break;
				case 'prestasi':
					$info['table'] = 'ms_prestasimhs';
					$info['key'] = 'idprestasi';
					$info['label'] = 'Penghargaan';
					break;
				case 'skors':
					$info['table'] = 'ak_skors';
					$info['key'] = 'idskors';
					$info['label'] = 'skors';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		// beasiswa
		function getBeasiswa($conn,$key,$label='',$post='') {
			$sql = "select p.idbeasiswa, p.nim, b.kodesumber, b.periodeawal, b.periodeakhir, b.tglawal, b.tglakhir, b.jumlahperperiode
					from ".static::table('ak_penerimabeasiswa')." p
					join ".static::table('ak_beasiswa')." b on p.idbeasiswa = b.idbeasiswa
					where p.nim = '$key' order by periodeawal desc";

			return static::getDetail($conn,$sql,$label,$post);
		}

		// penghargaan
		function getPenghargaan($conn,$key,$label='',$post='') {
			$sql = "select idpenghargaan, tglpenghargaan, namapenghargaan,namapenghargaanenglish, isvalid,idjenispenghargaan,filesertifikat
					from ".static::table('ak_penghargaan')."
					where nim = '$key' order by tglpenghargaan asc";

			return static::getDetail($conn,$sql,$label,$post);
		}

		// skors
		function getSkors($conn,$key,$label='',$post='') {
			$sql = "select idskors, periodeawal, periodeakhir, tglawal, tglakhir, alasanskors, keterangan
					from ".static::table('ak_skors')."
					where nim = '$key' order by periodeawal desc";

			return static::getDetail($conn,$sql,$label,$post);
		}

		// poin kemahasiswaan
		function getPoinMhs($conn,$key) {
			$sql = "select *
					from kemahasiswaan.mw_poinmhs
					where nim = '$key' ";

			return $conn->GetRow($sql);
		}

		// mendapatkan nama mahasiswa
		function getNama($conn,$nim,$cekstatus=true) {
			$sql = "select nama from ".static::table()." where ".static::getCondition($nim);
			if($cekstatus)
				$sql .= " and statusmhs in ('A','C','T')";

			return $conn->GetOne($sql);
		}

		function getNamaPendaftar($conn,$nopendaftar) {
			$sql = "select nama from pendaftaran.pd_pendaftar where nopendaftar = ".Query::escape($nopendaftar);

			return $conn->GetOne($sql);
		}

		// mendapatkan angkatan mahasiswa
		function getAngkatan($conn,$nim) {
			$sql = "select substring(periodemasuk,1,4) from ".static::table()." where ".static::getCondition($nim);

			return $conn->GetOne($sql);
		}

		// menghitung kurikulum mahasiswa
		function getKurikulum($conn,$periodedaftar,$kodeunit='') {
			if(!empty($kodeunit)) {
				$sql = "select thnkurikulum from ".static::table('ak_kurikulum')." where thnkurikulum <= '".substr($periodedaftar,0,4)."'
						and kodeunit = '$kodeunit' order by thnkurikulum desc limit 1";
				$data = $conn->GetOne($sql);
			}
			if(empty($data)) {
				$sql = "select thnkurikulum from ".static::table('ms_thnkurikulum')." where thnkurikulum <= '".substr($periodedaftar,0,4)."'
						order by thnkurikulum desc limit 1";
				$data = $conn->GetOne($sql);
			}
			return $data;
		}

		// mendapatkan periode masuk dari nim
		function getPeriodeMasukNIM($nim) {
			$thn = substr($nim,4,2);
			$now = date('Y');
			$fnow = substr($now,0,2);

			if($thn > substr($now,-2))
				return ($fnow-1).$thn.'1';
			else
				return $fnow.$thn.'1';
		}

		// mendapatkan indeks nilai mahasiswa
		function getIndeksNilai($conn,$nim) {
			/* $sql = "select ".static::schema.".f_ipk(nim) as ipk, ".static::schema.".f_ipslalu(nim) as ipslalu,
					".static::schema.".f_skslalu(nim) as skslalu, tipemhs from ".static::table()."
					where ".static::getCondition($nim);
			$data = $conn->GetRow($sql);

			if(empty($data['tipemhs']))
				$data['tipemhs'] = 'S1';

			$sql = "select f_batassks('".$data['ipslalu']."','".$data['skslalu']."','".$data['tipemhs']."') as batasbaru";
			$data['batasbaru'] = $conn->GetOne($sql); */

			$sql = "select ipk, ipslalu, skslalu, batassks as batasbaru,kodeunit from ".static::table()."
					where ".static::getCondition($nim);
			$data = $conn->GetRow($sql);

			return $data;
		}

		// mendapatkan nim awal
		function getFirstNIM($conn,$nim,$nip='') {
			$sql = "select min(nim) from ".static::table();
			if(!empty($nip))
				$sql .= " and nipdosenwali = '$nip'";
			$newnim = $conn->GetOne($sql);

			if(empty($newnim))
				return $nim;
			else
				return $newnim;
		}

		// mendapatkan nim sebelumnya
		function getPrevNIM($conn,$nim,$nip='') {
			$sql = "select max(nim) from ".static::table()." where nim < '$nim'";
			if(!empty($nip))
				$sql .= " and nipdosenwali = '$nip'";
			$newnim = $conn->GetOne($sql);

			if(empty($newnim))
				return $nim;
			else
				return $newnim;
		}

		// mendapatkan nim selanjutnya
		function getNextNIM($conn,$nim,$nip='') {
			$sql = "select min(nim) from ".static::table()." where nim > '$nim'";
			if(!empty($nip))
				$sql .= " and nipdosenwali = '$nip'";
			$newnim = $conn->GetOne($sql);

			if(empty($newnim))
				return $nim;
			else
				return $newnim;
		}

		// mendapatkan nim akhir
		function getLastNIM($conn,$nim,$nip='') {
			$sql = "select max(nim) from ".static::table();
			if(!empty($nip))
				$sql .= " and nipdosenwali = '$nip'";
			$newnim = $conn->GetOne($sql);

			if(empty($newnim))
				return $nim;
			else
				return $newnim;
		}

		// mendapatkan nim berdasarkan rfid
		function getNIMByRFID($conn,$rfid) {
			$sql = "select nim from ".static::table()." where rfid = ".Query::escape($rfid);

			return $conn->GetOne($sql);
		}

		// mendapatkan data array
		function getArray($conn,$unit='',$periode='') {
			/* if(!empty($unit)) {
				$info = $conn->GetRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$unit'");
				$sql = "select p.nim, p.nama||' ('||p.nim||')' from ".static::table()." p join gate.ms_unit u on p.kodeunit = u.kodeunit and
						u.infoleft >= '".$info['infoleft']."' and u.inforight <= '".$info['inforight']."'";
			}
			else */
				$sql = "select nim, nama||' ('||nim||')' from ".static::table();

			$a_where = array();
			if(!empty($unit))
				$a_where[] = "kodeunit = '$unit'";
			if(!empty($periode))
				$a_where[] = "periodemasuk = '$periode'";
			if(!empty($a_where))
				$sql .= " where ".implode(' and ',$a_where);

			$sql .= " order by nama";

			return Query::arrQuery($conn,$sql);
		}

		// angkatan
		function angkatan($conn) {
			$sql = "select distinct substring(periodemasuk,1,4) from ".static::table()."
					where periodemasuk is not null order by substring(periodemasuk,1,4) desc";

			return Query::arrQuery($conn,$sql);
		}

		// golongan
		function golongan() {
			$data = array('1' => 'PNS/ABRI/TNI', '2' => 'Anak PNS/ABRI/TNI', '3' => 'Umum');

			return $data;
		}

		// jalur penerimaan
		function jalurPenerimaan($conn) {
			require_once(Route::getModelPath('jalurpenerimaan'));

			return mJalurPenerimaan::getArray($conn);
		}

		// apakah pernah ponpes
		function pernahPonpes() {
			$data = array('1' => 'Ya', '0' => 'Tidak');

			return $data;
		}


		function getPindahan(){
			$data=array('0'=>'Lulusan SMU','1'=>'Transfer dari Prodi Lain (di Esa Unggul)','2'=>'Transfer Dari Univ. Lain');
			return $data;
		}
		// sistem kuliah
		function sistemKuliah($conn) {
			require_once(Route::getModelPath('sistemkuliah'));

			return mSistemKuliah::getArray($conn);
		}

		// status kerja
		function statusKerja() {
			$data = array('0' => 'Belum Kerja', '1' => 'Sudah Kerja');

			return $data;
		}
		function getJenjang($conn,$unit){
			$sql="select kode_jenjang_studi from ".static::table('ak_prodi')." where kodeunit='$unit'";
			return $conn->GetOne($sql);
		}
		function getJenjangByNim($conn,$nim){
			$sql="select kode_jenjang_studi from ".static::table('ak_prodi')." where kodeunit=(select kodeunit from ".static::table()." where nim ='$nim') ";
			return $conn->GetOne($sql);
		}
		function getHp($conn,$nim){
			$sql="select coalesce(hp,hp2) as nohp,coalesce(telp,telp2) as notelephon from ".static::table()." where nim='$nim'";
			return $conn->GetRow($sql);
		}
		function getGelombangDaftar($conn){
			$sql="select idgelombang,namagelombang from pendaftaran.lv_gelombang order by namagelombang";

			return Query::arrQuery($conn,$sql);
		}

		function getTagihanMhs($conn,$nim){
			$sql="select m.idtagihan,m.periode,m.flaglunas,m.jenistagihan,m.nominaltagihan,m.tgllunas
				from h2h.ke_tagihan m where m.nim = '$nim' order by idtagihan";

			return $conn->GetArray($sql);
		}

		function getPassMhs($conn,$nim){
			$sql="select password from gate.sc_user where username='$nim'";

			return $conn->GetOne($sql);
		}

				// menemukan data, untuk autocomplete
		function findUser($conn,$str,$col='',$key='') {
			global $conf;
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;

			$sql = "select $key, $col as label from ".static::table()." m
					join gate.sc_user u on m.nim = u.username
					where statusmhs = 'A' and lower($col::varchar) like '%$str%' order by ".static::order;
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);

			$data = array();
			while($row = $rs->FetchRow()) {
				$t_key = $row[$key];
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}

			return $data;
		}

		// menemukan data, untuk autocomplete
		function findPendaftar($conn,$str,$col='',$key='') {
			global $conf;

			$sql = "select $key, $col as label from pendaftaran.pd_pendaftar
					where lower($col::varchar) like '%$str%' order by nopendaftar";
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);

			$data = array();
			while($row = $rs->FetchRow())
				$data[] = array('key' => $row[$key], 'label' => $row['label']);

			return $data;
		}
	}

	class mMahasiswaWali extends mMahasiswa {
		// mendapatkan kueri list
		function listQuery() {
			$periode = Akademik::getPeriode();

			$sql = "select m.nim, m.periodemasuk, m.nama, m.sex, m.kodeunit, m.sistemkuliah, m.semestermhs, m.statusmhs, m.skslulus,
					p.prasyaratspp, p.frsterisi, p.frsdisetujui, substring(m.periodemasuk,1,4) as angkatan, u.namaunit
					from ".static::table()." m
					join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join ".static::table('ak_perwalian')." p on p.nim = m.nim and p.periode = '$periode'";

			return $sql;
		}

		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'angkatan': return "substring(m.periodemasuk,1,4) = '$key'";
				case 'pembimbing': return "m.nipdosenwali = '$key' and m.statusmhs not in ('L','U','W','O','K')";
			}
		}
	}

	// database host 2 host
	class mMahasiswaH2H extends mModel {
		const schema = 'public';
		const table = 'ms_mahasiswa';
		const order = 'nim';
		const key = 'nim';
		const label = 'mahasiswa H2H';

		// mendapatkan kueri list
		function listQuery() {
			$sql = "select nim, nama, m.fakultas, namajurusan, kelamin from ".static::table()." m
					left join ".static::table('ms_unit')." u on m.jurusan = u.jurusan";

			return $sql;
		}

		// sinkronisasi data
		function syncMahasiswa($conn,$connh) {
			$conn->BeginTrans();
			$connh->BeginTrans();

			$a_unit = self::unit($connh);

			$a_fakultas = array();
			foreach($a_unit as $t_fakultas => $t_unit)
				foreach($t_unit['data'] as $t_jurusan => $t_namajurusan)
					$a_fakultas[$t_jurusan] = $t_fakultas;

			// ambil log dari akademik
			$sql = "select synch2htime from akademik.ms_setting where idsetting = 1";

			$t_log = $conn->GetOne($sql);
			$t_now = date('Y-m-d H:i:s');

			// update log di akademik
			$sql = "update akademik.ms_setting set synch2htime = '$t_now'";
			$ok = $conn->Execute($sql);

			// sinkronisasi mahasiswa, insert saja
			if($ok) {
				$sql = "select nim, nama, sex, kodeunit from akademik.ms_mahasiswa".
						(empty($t_log) ? '' : " where t_updatetime > '$t_log'");
				$rs = $conn->Execute($sql);

				$a_data = array();
				while($row = $rs->FetchRow()) {
					if(empty($a_fakultas[$row['kodeunit']])) {
						$row['fakultas'] = '';
						$row['kodeunit'] = '';
					}
					else
						$row['fakultas'] = $a_fakultas[$row['kodeunit']];

					$t_data = array();
					$t_data[] = CStr::cStrNullS($row['nim'],false);
					$t_data[] = CStr::cStrNullS($row['nama'],false);
					$t_data[] = CStr::cStrNullS($row['fakultas'],false);
					$t_data[] = CStr::cStrNullS($row['kodeunit'],false);
					$t_data[] = CStr::cStrNullS($row['sex'],false);

					$a_data[] = '('.implode(',',$t_data).')';
				}

				if(!empty($a_data)) {
					$sql = "select * into temp ms_mahasiswa_temp from ms_mahasiswa limit 0;
							insert into ms_mahasiswa_temp values ".implode(',',$a_data).";
							insert into ms_mahasiswa
								select t.* from ms_mahasiswa_temp t
								left join ms_mahasiswa m on m.nim = t.nim
								where m.nim is null";
					$ok = $connh->Execute($sql);
				}
			}

			$conn->CommitTrans($ok);
			$connh->CommitTrans($ok);
		}

		// array unit
		function unit($conn) {
			$cek = Modul::getLeftRight();

			$sql = "select jurusan, fakultas, namajurusan from public.ms_unit
					order by fakultas, jurusan";
			$rs = $conn->Execute($sql);

			$data = array();
			while($row = $rs->FetchRow()) {
				if($t_fakultas != $row['fakultas']) {
					$t_fakultas = $row['fakultas'];
					$data[$t_fakultas] = array('label' => $t_fakultas, 'data' => array());
				}

				$data[$t_fakultas]['data'][$row['jurusan']] = $row['namajurusan'];
			}

			return $data;
		}

	}
?>
