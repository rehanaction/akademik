<?php
	// model pegawai
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));
	
	class mPegawai extends mBiodata {
		const schema = 'sdm';
		const table = 'ms_pegawai';
		const order = 'namadepan';
		const key = 'idpegawai';
		const label = 'pegawai';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select a.namaagama,r.*,akademik.f_namalengkap(r.gelardepan,r.namadepan,r.namatengah,r.namabelakang,r.gelarbelakang) as nama,u.namaunit,pk.namapangkat,s.jabatanstruktural, f.*,tp.tipepeg,jp.* from ".self::table()." r 
					join sdm.ms_unit us on r.idunit=us.idunit
					join gate.ms_unit u on us.kodeunit::text = u.kodeunit
					LEFT JOIN sdm.ms_pangkat pk ON r.idpangkat = pk.idpangkat
					LEFT JOIN sdm.ms_tipepeg tp ON r.idtipepeg = tp.idtipepeg
					LEFT JOIN sdm.ms_jenispeg jp ON r.idjenispegawai = jp.idjenispegawai
					LEFT JOIN sdm.ms_struktural s ON s.idjstruktural=r.idjstruktural
					LEFT JOIN sdm.ms_fungsional f ON f.idjfungsional=r.idjfungsional
					LEFT JOIN sdm.lv_agama a on a.idagama=r.idagama";
			return $sql;
		}

		function getDosen($conn,$key='') {
			$key = Modul::getUserIDPegawai();
			$sql = "select a.namaagama,r.*, akademik.f_namalengkap(r.gelardepan,r.namadepan,r.namatengah,r.namabelakang,r.gelarbelakang) as nama, f.keterangan as jafung from ".self::table()." r 
					join sdm.ms_unit us on r.idunit=us.idunit
					join gate.ms_unit u on us.kodeunit::text = u.kodeunit
					LEFT JOIN sdm.ms_pangkat pk ON r.idpangkat = pk.idpangkat
					LEFT JOIN sdm.ms_struktural s ON s.idjstruktural=r.idjstruktural
					LEFT JOIN sdm.ms_fungsional f ON r.idjfungsional=f.idjfungsional
					LEFT JOIN sdm.lv_agama a on a.idagama=r.idagama
					where ".static::getCondition($key);
					;
			$row = $conn->GetRow($sql);
			return $row;
		}
		
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data['unit'] = 'u.kodeunit';
			
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

		function getDataDetail($conn,$id){
			$sql = "select * from sdm.ms_pegawai where idpegawai='$id'";
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			/*
			$sql = "select p.*, substring(p.periodeaktif,1,4) as tahunaktif, substring(p.periodeaktif,5,1) as semesteraktif,
					substring(p.periodeajar,1,4) as tahunajar, substring(p.periodeajar,5,1) as semesterajar, substring(k.idkelurahan,1,2) as kodepripinsi
					from ".static::table()." p
					left join sdm.lv_kelurahan k on k.idkelurahan = p.idkelurahan
					where ".static::getCondition($key);
			*/
			$sql = "select p.*, substring(k.idkelurahan,1,2) as kodepripinsi,u.kodeunit
					from ".static::table()." p
					left join sdm.lv_kelurahan k on k.idkelurahan = p.idkelurahan
					left join sdm.ms_unit u on p.idunit=u.idunit
					where p.idpegawai::text = '$key'";
			return $sql;
		}
		
		// pendidikan
		const keyPendidikan = 'idpegawai,nopendidikan';
		
		function getListPendidikan($conn,$nik) {
			$sql = "select lj.namapendidikan, rp.* from ".static::table('pe_rwtpendidikan')." rp left join sdm.ms_pegawai mp on mp.idpegawai=rp.idpegawai 
					left join sdm.lv_jenjangpendidikan lj on lj.idpendidikan=rp.idpendidikan where (mp.idpegawai::text = '$nik') order by nourutrpen";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		function getDataPendidikan($conn,$key) {
			$sql = "select * from ".static::table('pe_pendidikan')." where ".static::getCondition($key,self::keyPendidikan);
			var_dump($sql);
			die();
			return $conn->GetRow($sql);
		}

		function insertPendidikan($conn,$record) {
			return Query::recInsert($conn,$record,static::table('pe_rwtpendidikan'));
		}
		
		function updatePendidikan($conn,$record,$key) {
			return Query::recUpdate($conn,$record,static::table('pe_pendidikan'),static::getCondition($key,self::keyPendidikan));
		}
		
		function deletePendidikan($conn,$key) {
			return Query::qDelete($conn,static::table('pe_pendidikan'),static::getCondition($key,self::keyPendidikan));
		}
		
		function getNamaKotaUniversitas($conn,$key) {
			$sql = "select coalesce(u.namakota,k.namakota) from akademik.ms_universitas u
					left join akademik.ms_kota k on u.kodekota = k.kodekota
					where u.kodeuniversitas = '$key'";
			
			return $conn->GetOne($sql);
		}
		
		// kursus
		const keyKursus = 'idpegawai,nokursus';
		
		function getListKursus($conn,$nik) {
			$sql = "select * from ".static::table('pe_kursus')." where idpegawai::text = '$nik' order by nokursus";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		function getDataKursus($conn,$key) {
			$sql = "select * from ".static::table('pe_kursus')." where ".static::getCondition($key,self::keyKursus);
			
			return $conn->GetRow($sql);
		}

		function getRate($conn,$key) {
			$sql = "select ratehonor from ".static::table('ms_fungsional')." where idjfungsional='".$key."' ";
			
			return $conn->GetOne($sql);
		}
		
		function insertKursus($conn,$record) {
			return Query::recInsert($conn,$record,static::table('pe_kursus'));
		}
		
		function updateKursus($conn,$record,$key) {
			return Query::recUpdate($conn,$record,static::table('pe_kursus'),static::getCondition($key,self::keyKursus));
		}
		
		function deleteKursus($conn,$key) {
			return Query::qDelete($conn,static::table('pe_kursus'),static::getCondition($key,self::keyKursus));
		}
		
		// pangkat
		const keyPangkat = 'idpegawai,kodepangkat';
		
		function getListPangkat($conn,$nip) {
			$sql = "select * from ".static::table('pe_rwtpangkat')." pr left join sdm.ms_pegawai p on p.idpegawai=pr.idpegawai 
					left join sdm.ms_pangkat mp on mp.idpangkat=pr.idpangkat where (p.idpegawai::text='$nip') and isvalid='Y' order by nourutrp";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		function getDataPangkat($conn,$key) {
			$sql = "select * from ".static::table('pe_pangkat')." where ".static::getCondition($key,self::keyPangkat);
			
			return $conn->GetRow($sql);
		}
		
		function insertPangkat($conn,$record) {
			return Query::recInsert($conn,$record,static::table('pe_pangkat'));
		}
		
		function updatePangkat($conn,$record,$key) {
			return Query::recUpdate($conn,$record,static::table('pe_pangkat'),static::getCondition($key,self::keyPangkat));
		}
		
		function deletePangkat($conn,$key) {
			return Query::qDelete($conn,static::table('pe_pangkat'),static::getCondition($key,self::keyPangkat));
		}
		
		// jabatan
		const keyJabatan = 'nip,jabatan';
		
		function getListJabatan($conn,$nip) {
			// $sql = "select * from ".static::table('v_rwtjabatan')." where nip = '$nip' order by tglmulai";
			$sql = "select * from ".static::table('pe_rwtstruktural')." pr left join sdm.ms_pegawai p on p.idpegawai=pr.idpegawai 
					left join sdm.ms_struktural ms on ms.idjstruktural=pr.idjstruktural 
					left join sdm.ms_jenispejabat mj on mj.idjnspejabat=pr.idjnspejabat where (p.idpegawai::text = '$nip') and isvalid='Y' order by tmtmulai";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		function getDataJabatan($conn,$key) {
			$sql = "select * from ".static::table('v_rwtjabatan')." where ".static::getCondition($key,self::keyJabatan);
			
			return $conn->GetRow($sql);
		}
		
		function insertJabatan($conn,$record) {
			if($record['jenisjabatan'] == 'F') {
				$table = static::table('pe_jfungsional');
				
				$record['jabatanfungsional'] = $record['jabatan'];
			}
			else {
				$table = static::table('pe_jstruktural');
				
				$record['jabatanstruktural'] = $record['jabatan'];
			}
			
			return Query::recInsert($conn,$record,$table);
		}
		
		function updateJabatan($conn,$record,$key) {
			list($nip,$jabatan) = explode('|',$key);
			
			$jenisjabatan = $jabatan[0];
			$jabatan = substr($jabatan,2);
			
			if($jenisjabatan == 'F') {
				$table = static::table('pe_jfungsional');
				$condition = "nip = '$nip' and jabatanfungsional = '$jabatan'";
				
				$record['jabatanfungsional'] = $record['jabatan'];
			}
			else {
				$table = static::table('pe_jstruktural');
				$condition = "nip = '$nip' and jabatanstruktural = '$jabatan'";
				
				$record['jabatanstruktural'] = $record['jabatan'];
			}
			
			return Query::recUpdate($conn,$record,$table,$condition);
		}
		
		function deleteJabatan($conn,$key) {
			list($nip,$jabatan) = explode('|',$key);
			
			$jenisjabatan = $jabatan[0];
			$jabatan = substr($jabatan,2);
			
			if($jenisjabatan == 'F') {
				$table = static::table('pe_jfungsional');
				$condition = "nip = '$nip' and jabatanfungsional = '$jabatan'";
			}
			else {
				$table = static::table('pe_jstruktural');
				$condition = "nip = '$nip' and jabatanstruktural = '$jabatan'";
			}
			
			return Query::qDelete($conn,$table,$condition);
		}
		
		// penghargaan
		const keyPenghargaan = 'nip,nopenghargaan';
		
		function getListPenghargaan($conn,$nip) {
			$sql = "select * from ".static::table('pe_penghargaan')." pp left join sdm.ms_pegawai p on p.idpegawai=pp.idpegawai 
					left join sdm.lv_penghargaan lp on lp.idpenghargaan=pp.idpenghargaan where (p.idpegawai::text = '$nip') order by nourutpenghargaan";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		function getDataPenghargaan($conn,$key) {
			$sql = "select * from ".static::table('pe_penghargaan')." where ".static::getCondition($key,self::keyPenghargaan);
			
			return $conn->GetRow($sql);
		}
		
		function insertPenghargaan($conn,$record) {
			return Query::recInsert($conn,$record,static::table('pe_penghargaan'));
		}
		
		function updatePenghargaan($conn,$record,$key) {
			return Query::recUpdate($conn,$record,static::table('pe_penghargaan'),static::getCondition($key,self::keyPenghargaan));
		}
		
		function deletePenghargaan($conn,$key) {
			return Query::qDelete($conn,static::table('pe_penghargaan'),static::getCondition($key,self::keyPenghargaan));
		}
		
		// kunjungan luar negeri
		const keyKunjungan = 'nip,nokunjungan';
		
		function getListKunjungan($conn,$nip) {
			$sql = "select * from ".static::table('pe_kunjunganluar')." where nip = '$nip' order by nokunjungan";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		function getDataKunjungan($conn,$key) {
			$sql = "select * from ".static::table('pe_kunjunganluar')." where ".static::getCondition($key,self::keyKunjungan);
			
			return $conn->GetRow($sql);
		}
		
		function insertKunjungan($conn,$record) {
			return Query::recInsert($conn,$record,static::table('pe_kunjunganluar'));
		}
		
		function updateKunjungan($conn,$record,$key) {
			return Query::recUpdate($conn,$record,static::table('pe_kunjunganluar'),static::getCondition($key,self::keyKunjungan));
		}
		
		function deleteKunjungan($conn,$key) {
			return Query::qDelete($conn,static::table('pe_kunjunganluar'),static::getCondition($key,self::keyKunjungan));
		}
		
		// keluarga
		const keyKeluarga = 'nip,nokeluarga,jeniskeluarga';
		
		function getListKeluarga($conn,$nip) {
			$sql = "select * from ".static::table('pe_keluarga')." where nip = '$nip' order by jeniskeluarga,nokeluarga";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['jeniskeluarga']][] = $row;
			
			return $data;
		}
		
		function getListSuamiIstri($conn,$nip) {
			$sql = "select pi.tgllahir as tgllahir2,pi.* from ".static::table('pe_istrisuami')." pi left join sdm.ms_pegawai p on p.idpegawai=pi.idpegawai 
					where p.idpegawai::text = '$nip' order by nourutist";
			$rs = $conn->Execute($sql);
			
			// $data_keluarga = array('istrisuami'=>'Suami/Istri', 'anak'=>'Anak');
			$data = array();
			// foreach($data_keluarga as $key => $value){	
				while($row = $rs->FetchRow())
					$data[] = $row;
			// }
			return $data;
		}
		
		function getListAnak($conn,$nip) {
			$sql = "select pa.tgllahir as tgllahiranak,pa.* from ".static::table('pe_anak')." pa left join sdm.ms_pegawai p on p.idpegawai=pa.idpegawai 
					where p.idpegawai::text='$nip' order by nourutanak";
			$rs = $conn->Execute($sql);
			
			// $data_keluarga = array('istrisuami'=>'Suami/Istri', 'anak'=>'Anak');
			$data = array();
			// foreach($data_keluarga as $key => $value){	
				while($row = $rs->FetchRow())
					$data[] = $row;
			// }
			return $data;
		}
		
		function getDataKeluarga($conn,$key) {
			$sql = "select * from ".static::table('pe_keluarga')." where ".static::getCondition($key,self::keyKeluarga);
			
			return $conn->GetRow($sql);
		}
		
		function insertKeluarga($conn,$record) {
			return Query::recInsert($conn,$record,static::table('pe_keluarga'));
		}
		
		function updateKeluarga($conn,$record,$key) {
			return Query::recUpdate($conn,$record,static::table('pe_keluarga'),static::getCondition($key,self::keyKeluarga));
		}
		
		function deleteKeluarga($conn,$key) {
			return Query::qDelete($conn,static::table('pe_keluarga'),static::getCondition($key,self::keyKeluarga));
		}
		
		// organisasi
		const keyOrganisasi = 'nip,noorganisasi,masaorganisasi';
		
		function getListOrganisasi($conn,$nip) {
			$sql = "select * from ".static::table('pe_organisasi')." o join ".static::table()." p on o.idpegawai=p.idpegawai where  p.idpegawai::text='$nip' order by o.nourutpo";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['masaorganisasi']][] = $row;
			
			return $data;
		}
		
		function getDataOrganisasi($conn,$key) {
			$sql = "select * from ".static::table('pe_organisasi')." where ".static::getCondition($key,self::keyOrganisasi);
			
			return $conn->GetRow($sql);
		}
		
		function insertOrganisasi($conn,$record) {
			return Query::recInsert($conn,$record,static::table('pe_organisasi'));
		}
		
		function updateOrganisasi($conn,$record,$key) {
			return Query::recUpdate($conn,$record,static::table('pe_organisasi'),static::getCondition($key,self::keyOrganisasi));
		}
		
		function deleteOrganisasi($conn,$key) {
			return Query::qDelete($conn,static::table('pe_organisasi'),static::getCondition($key,self::keyOrganisasi));
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'pendidikan':
					$info['table'] = 'ak_pendidikandosen';
					$info['key'] = 'nopendidikan';
					$info['label'] = 'pendidikan dosen';
					break;
				case 'keahlian':
					$info['table'] = 'ak_keahliandosen';
					$info['key'] = 'bidangkeahlian,nip';
					$info['label'] = 'bidang keahlian dosen';
					break;
				case 'ppm':
					$info['table'] = 'ak_ppmdosen';
					$info['key'] = 'noppm';
					$info['label'] = 'penelitian dosen';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// pendidikan
		function getPendidikan($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('ak_pendidikandosen')." where nip = '$key' order by kodependidikan, namapt";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// bidang keahlian
		function getBidangKeahlian($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('ak_keahliandosen')." where nip = '$key' order by bidangkeahlian";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// penelitian
		function getPenelitian($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('ak_ppmdosen')." where nip = '$key' order by tahunkegiatan, idtipeppm, judul";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// mendapatkan nama pegawai
		function getNamaPegawai($conn,$id) {
			$sql = "select akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as nama
					from ".static::table()." where idpegawai::text = '$id'";
			
			return $conn->GetOne($sql);
		}

		function getNamaPegawai2($conn,$id) {
			$sql = "select akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as nama
					from ".static::table()." where username::text = '$id'";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan array data
		function getArray($conn,$unit='') {
			if(!empty($unit)) {
				$info = $conn->GetRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$unit'");
				$sql = "select p.idpegawai, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama 
						from ".static::table()." p 
						join gate.ms_unit u on p.kodeunit = u.kodeunit and
						u.infoleft >= '".$info['infoleft']."' and 
						u.inforight <= '".$info['inforight']."' 
						order by nama";
			}
			else
				$sql = "select idpegawai, akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as nama from ".static::table()." order by nama";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getArrayLengkap($conn,$unit='') {
			if(!empty($unit)) {
				$info = $conn->GetRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$unit'");
				$sql = "select p.idpegawai, p.idpegawai::text||' - '||akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama from ".static::table()." 
						join gate.ms_unit u on p.kodeunit = u.kodeunit and
						u.infoleft >= '".$info['infoleft']."' and u.inforight <= '".$info['inforight']."' order by nama";
			}
			else
				$sql = "select idpegawai, idpegawai::text||' - '||akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang),akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as nama from ".static::table()." order by nama";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// bidang keahlian
		function bidangKeahlian($conn) {
			require_once(Route::getModelPath('keahlian'));
			
			return mKeahlian::getArray($conn);
		}
		
		// jabatan struktural
		function jabatanStruktural($conn) {
			require_once(Route::getModelPath('struktural'));
			
			return mStruktural::getArray($conn);
		}
		
		// jabatan fungsional
		function jabatanFungsional($conn) {
			require_once(Route::getModelPath('fungsional'));
			
			return mFungsional::getArray($conn);
		}
		
		// jenis jabatan
		function jenisJabatan() {
			$data = array('F' => 'Fungsional', 'S' => 'Struktural');
			
			return $data;
		}

		function tipeJabatan() {
			$data = array('F' => 'Fungsional', 'S' => 'Struktural');
			
			return $data;
		}
		
		// jenis keluarga
		function jenisKeluarga($conn) {
			require_once(Route::getModelPath('jeniskeluarga'));
			
			return mJenisKeluarga::getArray($conn);
		}
		
		// masa organisasi
		function masaOrganisasi($conn) {
			require_once(Route::getModelPath('masaorganisasi'));
			
			return mMasaOrganisasi::getArray($conn);
		}
		
		// pangkat
		function pangkat($conn) {
			require_once(Route::getModelPath('pangkat'));
			
			return mPangkat::getArray($conn);
		}
		
		// status pegawai
		function statusPegawai($conn) {
			require_once(Route::getModelPath('statuspeg'));
			
			return mStatusPeg::getArray($conn);
		}
		
		// status tetap
		function statusTetap($conn) {
			require_once(Route::getModelPath('statustetap'));
			
			return mStatusTetap::getArray($conn);
		}
		
		// tingkat keahlian
		function tingkatKeahlian() {
			$data = array('B' => 'Basic', 'I' => 'Intermediate', 'E' => 'Expert');
			
			return $data;
		}
		
		// tipe pegawai
		function tipePegawai($conn) {
			require_once(Route::getModelPath('jenispeg'));
			
			return mJenisPeg::getArray($conn);
		}

		function jaFung($conn) {
			require_once(Route::getModelPath('jafung'));
			
			return mJafung::getArrayCombo($conn);
		}

		function rateHonor($conn) {
			require_once(Route::getModelPath('ratehonor'));
			
			return mRateHonor::getArrayCombo($conn);
		}

		function jenisPegawai2($conn) {
			require_once(Route::getModelPath('jenispeg2'));
			
			return mJenisPeg2::getArray($conn);
		}

		function kodeUnitsdm($conn) {
			require_once(Route::getModelPath('idunit'));
			
			return mIdunit::getArray($conn);
		}

		function jenjangPend($conn) {
			require_once(Route::getModelPath('jenjang'));
			
			return mJenjang::getArray($conn);
		}

		function universitas($conn) {
			require_once(Route::getModelPath('univ'));
			
			return mUniv::getArray($conn);
		}
		
		// tipe ppm
		function tipePPM($conn) {
			require_once(Route::getModelPath('jenisppm'));
			
			return mJenisPPM::getArray($conn);
		}
		
		// menemukan data dosen, untuk autocomplete
		function findDosen($conn,$str,$col='',$key='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
			
			$sql = "select $key, $col as label from ".static::table()."
					where isdosen=-1 and idstatusaktif='AA' and lower($col::varchar) like '%$str%' order by ".static::order;
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row[$key];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			
			return $data;
		}
		function getIdPegawai($conn, $nik){
			$sql="select idpegawai from ".static::table()." where idpegawai::text='$nik'";
			return $conn->getOne($sql);
		}
		function getAgama($conn,$idagama){
			$namaagama = $conn->GetOne("select namaagama from sdm.lv_agama where idagama='$idagama'");
			return $namaagama;
		}
		
		function getStatusNikah($conn,$idstatus){
			if($idstatus=='S')
				return 'Single';
			if($idstatus=='D')
				return 'Duda';
			if($idstatus=='J')
				return 'Janda';
			if($idstatus=='N')
				return 'Nikah';
		}
		function periodeGaji($conn_sdm){
			$sql="select periodegaji,namaperiode from sdm.ga_periodegaji";
			
			return Query::arrQuery($conn_sdm,$sql);
		} 
		function getTarifDosen($conn_sdm,$nodosen){
		$sql="select  p.idpegawai,p.nip,p.nodosen,p.namadepan,p.namatengah,p.namabelakang,p.norekening,
				p.norekeninghonor,p.anrekeninghonor, p.npwp,p.idhubkerja,a.biatrans,
				coalesce(a.procpphmanual,a.procpph) as pph,r.kodejnsrate,nominal,jr.namajnsrate
				from sdm.ms_pegawai p
				left join sdm.ga_ajardosen a on p.idpegawai=a.idpegawai 
				left join sdm.ga_ratehonor r on r.idpegawai=p.idpegawai /*and r.isvalid='Y'*/
				left join sdm.ms_jnsrate jr on r.kodejnsrate=jr.kodejnsrate
				where a.isvalid='Y' and p.nodosen='$nodosen'";
			
		$sdm=$conn_sdm->GetArray($sql);
		$a_pegawai=array();
		foreach($sdm as $row){
			
			$a_pegawai[$row['kodejnsrate']]=array(
											'nodosen'=>$row['nodosen'],
											'namajnsrate'=>$row['namajnsrate'],
											'nominal'=>$row['nominal'],
											'norekening'=>$row['norekening'],
											'norekeninghonor'=>$row['norekeninghonor'],
											'npwp'=>$row['npwp'],
											'anrekeninghonor'=>$row['anrekeninghonor'],
											'biatrans'=>$row['biatrans'],
											'pph'=>$row['pph'],
											'statuspegawai'=>$row['idhubkerja']);
		}
		return $a_pegawai;
	}
	
	function getDataDosen($conn_sdm,$nodosen){
		
		$sql="select  p.idpegawai,p.nip,p.nodosen,p.namadepan,p.namatengah,p.namabelakang,p.norekening,
				p.norekeninghonor,p.anrekeninghonor, p.npwp,p.idhubkerja,a.biatrans,
				coalesce(a.procpphmanual,a.procpph) as pph
				from sdm.ms_pegawai p
				left join sdm.ga_ajardosen a on p.idpegawai=a.idpegawai 
				where ";
		if(is_array($nodosen))
			$sql.=" p.nodosen in ('".implode("','",$nodosen)."')";
		else if(!is_array($nodosen))
			$sql.=" p.nodosen='$nodosen'";
		
		$sdm=$conn_sdm->GetArray($sql);
		$a_pegawai=array();
		foreach($sdm as $row){
			
			$a_pegawai[$row['nodosen']]=array(
											'nodosen'=>$row['nodosen'],
											'norekening'=>$row['norekening'],
											'norekeninghonor'=>$row['norekeninghonor'],
											'npwp'=>$row['npwp'],
											'anrekeninghonor'=>$row['anrekeninghonor'],
											'biatrans'=>$row['biatrans'],
											'pph'=>$row['pph'],
											'statuspegawai'=>$row['idhubkerja']);
		}
		return $a_pegawai;
	}
	
	function getParamRate($conn_sdm){
		$data=$conn_sdm->GetArray("select * from sdm.ms_mapratehonor");
		$a_param=array();
		foreach($data as $row)
			$a_param[$row['sistemkuliah'].'|'.$row['nohari'].'|'.$row['jeniskuliah'].'|'.$row['isonline']]=$row['kodejnsrate'];
		return $a_param;
	}
	
	function arrPph($conn_sdm){
		$data=$conn_sdm->GetArray("select idhubkerja,isnpwp,prosentase from sdm.ms_procpphhonor");
		$arr=array();
		foreach($data as $row){
			$arr[$row['idhubkerja'].'|'.$row['isnpwp']]=$row['prosentase'];
		}
		return $arr;
	}
	//hard code untuk ambil kode jenis rate manual
	function rateManual($conn_sdm){
		$sql="select kodejnsrate from sdm.ms_jnsrate where kodejnsrate='R2' and ismanual='Y' and isaktif='Y'";
		
		return $conn_sdm->GetOne($sql);	
	}
	
	function getQueryRate($sort,$filter){
		$sql="select  p.idpegawai,p.nip,p.nodosen,
				(coalesce(p.namadepan,'')+' '+coalesce(p.namatengah,'')+' '+coalesce(p.namabelakang,'')) as namadosen,p.norekening,
				p.norekeninghonor,p.anrekeninghonor, p.npwp,p.idhubkerja,a.biatrans,
				coalesce(a.procpphmanual,a.procpph) as pph,r.kodejnsrate,nominal,jr.namajnsrate,a.isvalid
				from sdm.ms_pegawai p
				left join sdm.ga_ajardosen a on p.idpegawai=a.idpegawai 
				left join sdm.ga_ratehonor r on r.idpegawai=p.idpegawai /*and r.isvalid='Y'*/
				left join sdm.ms_jnsrate jr on r.kodejnsrate=jr.kodejnsrate";
				
		//return static::getListQuery($sort,$filter,$sql);
		return $sql;
	}
	
}
?>
