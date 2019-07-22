<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mRiwayat extends mModel {
		const schema = 'sdm';
		
		// mendapatkan kueri list riwayat pendidikan
		function listQueryRiwayatPendidikan($r_key) {
			$sql = "select r.*, p.namapendidikan,j.namajurusan,
					case when r.kodept is not null then t.namapt else namainstitusi end as namainstitusipend
					from ".self::table('pe_rwtpendidikan')." r 
					left join ".self::table('lv_jenjangpendidikan')." p on p.idpendidikan=r.idpendidikan
					left join ".self::table('ms_pt')." t on t.kodept=r.kodept
					left join ".self::table('ms_jurusan')." j on j.kodejurusan=r.kodejurusan
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapat kueri data riwayat pendidikan
		function getDataEditPendidikan($r_subkey) {
			$sql = "select r.*, p.namapendidikan,
					case when r.kodept is not null then t.namapt else namainstitusi end as pt,
					case when r.kodefakultas is not null then f.namafakultas else fakultas end as fakultas,
					case when r.kodejurusan is not null then j.namajurusan else jurusan end as jurusan,
					case when r.kodebidang is not null then b.namabidang else bidang end as bidang
					from ".self::table('pe_rwtpendidikan')." r
					left join ".self::table('lv_jenjangpendidikan')." p on p.idpendidikan=r.idpendidikan
					left join ".self::table('ms_pt')." t on t.kodept=r.kodept
					left join ".self::table('ms_fakultas')." f on f.kodefakultas=r.kodefakultas
					left join ".self::table('ms_jurusan')." j on j.kodejurusan=r.kodejurusan
					left join ".self::table('ms_bidang')." b on b.kodebidang=r.kodebidang
					where nourutrpen='$r_subkey'";
			
			return $sql;
		}
		
		function jenjangPendidikan($conn) {
			$sql = "select idpendidikan, namapendidikan from ".static::schema()."lv_jenjangpendidikan order by urutan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function lokasiPendidikan(){
			return array("L" => "Luar Negeri", "D" => "Dalam Negeri");
		}
		
		function letakGelar(){
			return array("D" => "Depan", "B" => "Belakang");
		}
		
		function diakuiUniversitas(){
			return array("Y" => "Ya", "T" => "Tidak");
		}
		
		function getPendidikanAkhir($conn,$r_key){
			$sql = "select idpendidikan from ".self::table('ms_pegawai')." where idpegawai = $r_key";
			
			return $conn->GetOne($sql);
		}

		function setGelar($conn,$r_key){
			$row = $conn->GetRow("select * from ".self::table('pe_rwtpendidikan')." where nourutrpen = $r_key");
	        if($row['isvalid'] == 'Y'){
	            if($row['letakgelar'] == 'D'){
	            	$sql = "select gelardepan from " . self::table('ms_pegawai') . " where idpegawai = ".$row['idpegawai'];
	                $adagelar = $conn->GetOne($sql);

	                if(empty($adagelar)){
	                	$sql = "update " . self::table('ms_pegawai') . " set gelardepan='".$row['gelar'].".' where idpegawai = ".$row['idpegawai'];
	                    $conn->Execute($sql);
	                }else{
		                $sql = "select gelardepan from " . self::table('ms_pegawai') . " where gelardepan like '%".$row['gelar']."%' and idpegawai = ".$row['idpegawai'];
		                $sudahada = $conn->GetOne($sql);

		                if(empty($sudahada)){
		                    $sql = "update " . self::table('ms_pegawai') . " set gelardepan=gelardepan||', ".$row['gelar']."' where idpegawai = ".$row['idpegawai'];
		                    $conn->Execute($sql);
		                }
		            }
	            }
	            else if($row['letakgelar'] == 'B'){
	                $sql = "select gelarbelakang from " . self::table('ms_pegawai') . " where gelarbelakang like '%".$row['gelar']."%' and idpegawai = ".$row['idpegawai'];
	                $sudahada = $conn->GetOne($sql);

	                if(empty($sudahada)){
	                    $sql = "update " . self::table('ms_pegawai') . " set gelarbelakang=gelarbelakang||', ".$row['gelar']."' where idpegawai = ".$row['idpegawai'];
	                    $conn->Execute($sql);
	                }
	            }
	        }
        
        return $conn->ErrorNo();
    }
		
		//istri suami
		function listQueryRiwayatIST($r_key) {
			$sql = "select r.*, case when r.jeniskelamin = 'P' then 'Perempuan' when r.jeniskelamin = 'L' then 'Laki-laki' else '' end as jnskelamin,
					case when r.statuspasangan = 'W' then 'Wafat' when r.statuspasangan = 'H' then 'Hidup' else '' end as stspasangan
					from ".self::table('pe_istrisuami')." r 
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//cek apakah sudah punya istri/ suami
		function cekPunyaIstriSuami($conn, $r_key){
			$ispunya = $conn->GetOne("select top 1 1 from ".self::table('pe_istrisuami')." where idpegawai = $r_key and isvalid = 'Y' and (statuspasangan is null or statuspasangan = 'H') and (iscerai is null or iscerai = 'T') order by tglkawin desc");
			
			$return = !empty($ispunya) ? true : false;
			
			return $return;
		}
		
		//anak
		function listQueryRiwayatAnak($r_key) {
			$sql = "select r.*, case when r.jeniskelamin = 'P' then 'Perempuan' when r.jeniskelamin = 'L' then 'Laki-laki' else '' end as jnskelamin
					from ".self::table('pe_anak')." r 
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//Status anak dalam keluarga
		function statusAnak(){
			$data = array('K' => 'Anak Kandung', 'T' => 'Anak Tiri', 'A' => 'Anak Adopsi');
			
			return $data;
		}
		
		// mendapatkan kueri list riwayat pangkat
		function listQueryRiwayatPangkat($r_key) {
			$sql = "select r.*,js.namajenissk,'('||p.golongan||') '||p.namapangkat as namagolongan,coalesce(cast(r.masakerjathn as varchar),'0')||' tahun '||coalesce(cast(r.masakerjabln as varchar),'0')||' bulan' as masakerja
					from ".self::table('pe_rwtpangkat')." r 
					left join ".self::table('ms_jenissk')." js on js.jenissk = r.jenissk
					left join ".self::table('ms_pangkat')." p on p.idpangkat = r.idpangkat
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		// mendapatkan kueri data riwayat pangkat
		function getDataEditPangkat($r_key) {
			$sql = "select r.*,js.namajenissk,'('||p.golongan||') '||p.namapangkat as namagolongan,coalesce(cast(r.masakerjathn as varchar),'0') as masakerjathn,coalesce(cast(r.masakerjabln as varchar),'0') as masakerjabln
					from ".self::table('pe_rwtpangkat')." r 
					left join ".self::table('ms_jenissk')." js on js.jenissk = r.jenissk
					left join ".self::table('ms_pangkat')." p on p.idpangkat = r.idpangkat
					where nourutrp='$r_key'";
			
			return $sql;
		}
		
		//cek apakah sudah ada acuan masa kerja
		function isExistAcuan($conn,$r_key,$r_subkey){
			$sql = "select top 1 1 from ".self::table('pe_rwtpangkat')." where idpegawai = $r_key and isvalid = 'Y' and flagacuan = 'Y'";	
			$sql .= !empty($r_subkey) ? " and nourutrp <> $r_subkey" : "";
			$acuan = $conn->GetOne($sql);
			
			$isacuan = !empty($acuan) ? true : false;
			
			return $isacuan;
		}
		
		/*
		function jenisKenaikanPangkat($conn) {
			$sql = "select idkenaikanpangkat, kenaikanpangkat from ".static::schema()."lv_kenaikanpangkat order by kenaikanpangkat";
			
			return Query::arrQuery($conn,$sql);
		}
		*/
		function namaPangkat($conn) {
			$sql = "select idpangkat, '('||golongan||') '||namapangkat as namagolongan from ".static::schema()."ms_pangkat order by idpangkat";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function jenisSK($conn) {
			$sql = "select jenissk, namajenissk from ".static::schema()."ms_jenissk order by namajenissk";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function jenisPangkat(){
			return array("U" => "Universitas", "K" => "Kopertis");
		}
		
		// mendapatkan kueri list riwayat fungsional 
		function listQueryRiwayatFungsional($r_key,$jenis) {
			$sql = "select r.*,j.jabatanfungsional
					from ".self::table('pe_rwtfungsional')." r 
					left join ".self::table('ms_fungsional')." j on j.idjfungsional = r.idjfungsional
					where idpegawai='$r_key' and r.jenisjabatan = '$jenis'";
			
			return $sql;
		}
				
		// mendapatkan kueri data riwayat fungsional
		function getDataEditFungsional($r_key,$jenis) {
			$sql = "select r.*,j.jabatanfungsional
					from ".self::table('pe_rwtfungsional')." r 
					left join ".self::table('ms_fungsional')." j on j.idjfungsional = r.idjfungsional
					where nourutjf='$r_key' and r.jenisjabatan = '$jenis'";
			
			return $sql;
		}
		
		function jabatanFungsional($conn) {
			$sql = "select idjfungsional, jabatanfungsional from ".static::schema()."ms_fungsional order by idjfungsional";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan kueri list riwayat struktural
		function listQueryRiwayatStruktural($r_key) {
			$sql = "select r.*,j.jabatanstruktural,p.jenispejabat
					from ".self::table('pe_rwtstruktural')." r 
					left join ".self::table('ms_struktural')." j on j.idjstruktural = r.idjstruktural
					left join ".self::table('ms_jenispejabat')." p on p.idjnspejabat = r.idjnspejabat
					where idpegawai='$r_key'";
			
			return $sql;
		}
				
		// mendapatkan kueri data riwayat struktural
		function getDataEditStruktural($r_key) {
			$sql = "select r.*,j.jabatanstruktural,p.jenispejabat
					from ".self::table('pe_rwtstruktural')." r 
					left join ".self::table('ms_struktural')." j on j.idjstruktural = r.idjstruktural
					left join ".self::table('ms_jenispejabat')." p on p.idjnspejabat = r.idjnspejabat
					where nourutjs='$r_key'";
			
			return $sql;
		}
		
		function jenisPejabat($conn) {
			$sql = "select idjnspejabat, jenispejabat from ".static::schema()."ms_jenispejabat order by idjnspejabat";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function isUtama(){
			return array("Y" => "Ya", "T" => "Tidak");
		}
		
		// mendapatkan kueri list riwayat mutasi
		function listQueryRiwayatMutasi($r_key) {
			$sql = "select r.*,ua.namaunit as namaunitasal,ut.namaunit as namaunittujuan,pa.jenispegawai as namajnspegasal,pt.jenispegawai as namajnspegtujuan
					from ".self::table('pe_rwtmutasi')." r 
					left join ".self::table('ms_unit')." ua on ua.idunit = r.unitasal
					left join ".self::table('ms_unit')." ut on ut.idunit = r.unittujuan
					left join ".self::table('ms_jenispeg')." pa on pa.idjenispegawai = r.jenispegasal
					left join ".self::table('ms_jenispeg')." pt on pt.idjenispegawai = r.jenispegtujuan
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
				
		// mendapatkan kueri data riwayat mutasi
		function getDataEditMutasi($r_key) {
			$sql = "select r.*,ua.namaunit as namaunitasal,ut.namaunit as namaunittujuan,pa.jenispegawai as namajnspegasal,pt.jenispegawai as namajnspegtujuan
					from ".self::table('pe_rwtmutasi')." r 
					left join ".self::table('ms_unit')." ua on ua.idunit = r.unitasal
					left join ".self::table('ms_unit')." ut on ut.idunit = r.unittujuan
					left join ".self::table('ms_jenispeg')." pa on pa.idjenispegawai = r.jenispegasal
					left join ".self::table('ms_jenispeg')." pt on pt.idjenispegawai = r.jenispegtujuan
					where nourutmutasi='$r_key'";
			
			return $sql;
		}
		
		function getPegawaiNow($conn,$r_key){
			$rowp = $conn->GetRow("select idunit,idjenispegawai from ".self::table('ms_pegawai')." where idpegawai = '$r_key'");
			
			return $rowp;
		}
		
		function jenisMutasi(){
			return array('P' => 'Promosi', 'D' => 'Demosi', 'R' => 'Rotasi');
		}
		
		//cek valid data Mutasi
		function cekValidMutasi($conn,$r_subkey){
			$cek = $conn->GetRow("select 1 from ".static::schema()."pe_rwtmutasi where nourutmutasi=$r_subkey and isvalid='Y'");
			
			return $cek;
		}
		
		// mendapatkan kueri list riwayat aktif
		function listQueryRiwayatAktif($r_key) {
			$sql = "select r.*,a1.namastatusaktif as namastatusaktifbaru,a2.namastatusaktif as namastatusaktiflama
					from ".self::table('pe_rwtaktif')." r 
					left join ".self::table('lv_statusaktif')." a1 on a1.idstatusaktif = r.statusaktifbaru
					left join ".self::table('lv_statusaktif')." a2 on a2.idstatusaktif = r.statusaktiflama
					where idpegawai='$r_key'";
			
			return $sql;
		}
				
		// mendapatkan kueri data riwayat aktif
		function getDataEditAktif($r_key) {
			$sql = "select r.*,a1.namastatusaktif as namastatusaktifbaru,a2.namastatusaktif as namastatusaktiflama
					from ".self::table('pe_rwtaktif')." r 
					left join ".self::table('lv_statusaktif')." a1 on a1.idstatusaktif = r.statusaktifbaru
					left join ".self::table('lv_statusaktif')." a2 on a2.idstatusaktif = r.statusaktiflama
					where nourut = '$r_key'";
			
			return $sql;
		}
		
		//mendapatkan status aktif pegawai
		function getStatusAktif($conn, $r_key){
			$status = $conn->GetOne("select idstatusaktif from ".static::schema()."ms_pegawai where idpegawai = $r_key");
			
			return $status;
		}
		
		//simpan status aktif pegawai
		function saveAktifPeg($conn,$idpegawai,$r_subkey=''){
			if(empty($r_subkey))
				$aktif = $conn->GetOne("select statusaktifbaru from ".self::table('pe_rwtaktif')." where idpegawai = $idpegawai and isvalid = 'Y' order by tglawal desc");
			else{
				$row = $conn->GetRow("select * from ".self::table('pe_rwtaktif')." where idpegawai = $idpegawai and isvalid = 'Y' order by tglawal desc");
				if($r_subkey == $row['nourut'])
					$aktif = $row['statusaktiflama'];
				else
					$aktif = $row['statusaktifbaru'];
			}
			
			if(!empty($aktif))
				$conn->Execute("update ".self::table('ms_pegawai')." set idstatusaktif = '$aktif' where idpegawai = $idpegawai");
			
			return $conn->ErrorNo();
		}
		
		// mendapatkan kueri list hubungan kerja
		function listQueryHubunganKerja($r_key) {
			$sql = "select r.*, h.hubkerja
					from ".self::table('pe_rwthubungankerja')." r 
					left join ".self::table('ms_hubkerja')." h on h.idhubkerja=r.idhubkerja
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapat data hubungan kerja
		function getDataEditHubunganKerja($r_subkey) {
			$sql = "select r.*, h.hubkerja
					from ".self::table('pe_rwthubungankerja')." r 
					left join ".self::table('ms_hubkerja')." h on h.idhubkerja=r.idhubkerja
					where nourutrwthub = '$r_subkey'";
			
			return $sql;
		}
		
		//mendapatkan jenis pegawai berdasarkan tipe pegawai
		function jenisPegawai($conn,$tipe) {
			$sql = "select idjenispegawai, jenispegawai from ".static::schema()."ms_jenispeg where idtipepeg = '$tipe' order by jenispegawai";
			
			return Query::arrQuery($conn,$sql);
		}

		function jenisPegawaiBaru($conn,$tipe) {
			$sql = "select idjenispegawai, jenispegawai from ".static::schema()."ms_jenispegbaru where idtipepeg = '$tipe' order by jenispegawai";
			
			return Query::arrQuery($conn,$sql);
		}

		function kelompokpeg($conn,$jenispegawai) {
			$sql = "select idkelompok, namakelompok from ".static::schema()."ms_kelompokpeg where idjenispegawai = '$jenispegawai' order by idkelompok";
			
			return Query::arrQuery($conn,$sql);
		}
		
		/****************************************************** C R O N ***************************************************/
		
		function cronStruktural($conn,$tgl){
			require_once(Route::getModelPath('integrasi'));
			
			$conn->Execute("update ".self::table('pe_rwtstruktural')." set isaktif = 'T' where tmtselesai = '$tgl'");
			
			//select jabatan yang sudah tidak aktif
			$sql = "select idpegawai,nourutjs from ".self::table('pe_rwtstruktural')." where isaktif = 'T' and tmtselesai = '$tgl'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				mIntegrasi::deletePejabatRole($conn,$row['idpegawai'],$row['nourutjs']);
			}
			
			return $conn->ErrorNo();
		}

		function cronEfektif($conn){
			$a_table = array('pe_rwtpangkat|tmtpangkat','pe_rwtfungsional|tmtmulai','pe_rwtbasedosen|tglmulai','pe_rwthubungankerja|tglefektif','pe_rwtmutasi|tmttugas','pe_sertifikasi|tglsertifikasi','pe_rwtstruktural|tmtmulai','pe_rwtaktif|tglakhir');

			foreach ($a_table as $value) {
				list($table,$tmt) = explode('|', $value);

				$conn->Execute("update ".self::table($table)." set isefektif = 'Y' where isefektif = null and $tmt <= CAST(GETDATE() AS DATE)");
				$err = $conn->ErrorNo();

				if($err)
					break;
			}
			
			return $conn->ErrorNo();
		}
		
		/******************************************************************************************************************/
		/***************************************************** L A P O R A N **********************************************/
		/******************************************************************************************************************/
		
		function repRiwayatPendidikan($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai, 
					u.namaunit, m.namapendidikan,case when r.kodept is not null then pt.namapt else namainstitusi end as institusi,t.tipepeg
					from ".static::schema()."pe_rwtpendidikan r 
					left join ".static::schema()."lv_jenjangpendidikan m on m.idpendidikan=r.idpendidikan
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg = p.idtipepeg
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_pt pt on pt.kodept=r.kodept
					where r.tglijazah between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tglijazah desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}	
		
		function repRiwayatPendidikanDosen($conn,$r_kodeunit){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			//data dosen
			$sql = "select p.idpegawai,p.nodosen,p.nidn,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					j.jenispegawai,h.namaunit
					from ".static::schema()."ms_pegawai p
					left join ".static::schema()."ms_jenispeg j on j.idjenispegawai = p.idjenispegawai
					left join ".static::schema()."ms_unit h on h.idunit=p.idunitbase
					where p.idtipepeg = 'D'
					and h.infoleft >= ".(int)$col['infoleft']." and h.inforight <= ".(int)$col['inforight']." 
					order by namapegawai";			
			$rs = $conn->Execute($sql);
			
			//riwayat pendidikan dosen
			$sql = "select r.*,case when r.kodefakultas is not null then f.namafakultas else fakultas end as fakultas,
					case when r.kodejurusan is not null then j.namajurusan else jurusan end as jurusan
					from ".static::schema()."pe_rwtpendidikan r 
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".self::table('ms_fakultas')." f on f.kodefakultas=r.kodefakultas
					left join ".self::table('ms_jurusan')." j on j.kodejurusan=r.kodejurusan
					left join ".self::table('lv_jenjangpendidikan')." mp on mp.idpendidikan=r.idpendidikan
					where p.idtipepeg = 'D' and r.idpendidikan in ('51','52','53')
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by mp.urutan desc";
			$rsr = $conn->Execute($sql);
			
			while($rowr = $rsr->FetchRow()){
				$data[$rowr['idpegawai']][$rowr['idpendidikan']]['F'] = $rowr['fakultas'];
				$data[$rowr['idpegawai']][$rowr['idpendidikan']]['J'] = $rowr['jurusan'];
				$data[$rowr['idpegawai']][$rowr['idpendidikan']]['G'] = $rowr['gelar'];
			}
			
			$a_data = array('list' => $rs,'data' => $data, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}	

		function repRiwayatPangkat($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_jenissk){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai, 
					u.namaunit, '('||g.golongan||') '||g.namapangkat as namagolongan,t.tipepeg,j.namajenissk,
					cast(r.masakerjathn as varchar)||' tahun '||cast(r.masakerjabln as varchar)||' bulan' as masakerja,jabatanfungsional
					from ".static::schema()."pe_rwtpangkat r 
					left join ".static::schema()."ms_pangkat g on g.idpangkat=r.idpangkat
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg
					left join ".static::schema()."ms_jenissk j on j.jenissk=r.jenissk
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=p.idjfungsional
					where r.tmtpangkat between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
					
			$sql .= !empty($r_jenissk) ? " and r.jenissk = '$r_jenissk'" : "";
			$sql .= " order by namapegawai,r.tmtpangkat desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}		
		
		function repRiwayatFungsional($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_jenisjab){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai, 
					u.namaunit, f.jabatanfungsional, t.tipepeg||' - '||j.jenispegawai as tipepeg
					from ".static::schema()."pe_rwtfungsional r 
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=r.idjfungsional
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunitbase
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg
					left join ".static::schema()."ms_jenispeg j on j.idjenispegawai=p.idjenispegawai
					where r.tmtmulai between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .= !empty($r_jenisjab) ? " and r.jenisjabatan = '$r_jenisjab'" : "";
			$sql .= " order by namapegawai,r.tmtmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}		
				
		function jenisJabatan(){
			return array("L" => "Akademik", "K" => "Kopertis");
		}
		
		function repRiwayatStruktural($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, s.jabatanstruktural,j.jenispejabat,t.tipepeg
					from ".static::schema()."pe_rwtstruktural r 
					left join ".static::schema()."ms_struktural s on s.idjstruktural=r.idjstruktural
					left join ".static::schema()."ms_jenispejabat j on j.idjnspejabat = r.idjnspejabat
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit	
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg							
					where r.tmtmulai between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tmtmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}	
		
		function repRiwayatMutasi($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, ua.namaunit as namaunitasal,ut.namaunit as namaunittujuan,
					pa.jenispegawai as namajnspegasal,pt.jenispegawai as namajnspegtujuan,t.tipepeg
					from ".static::schema()."pe_rwtmutasi r 
					left join ".static::schema()."ms_unit ua on ua.idunit = r.unitasal
					left join ".static::schema()."ms_unit ut on ut.idunit = r.unittujuan
					left join ".static::schema()."ms_jenispeg pa on pa.idjenispegawai = r.jenispegasal
					left join ".static::schema()."ms_jenispeg pt on pt.idjenispegawai = r.jenispegtujuan
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg										
					where r.tmttugas between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tmttugas desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}		
		
		function repRiwayatHubunganKerja($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, h.hubkerja,t.tipepeg,
					cast(r.masakerjathn as varchar)||' tahun '||cast(r.masakerjabln as varchar)||' bulan' as masakerja
					from ".static::schema()."pe_rwthubungankerja r 
					left join ".static::schema()."ms_hubkerja h on h.idhubkerja=r.idhubkerja
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg							
					where r.tglefektif between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tglefektif desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}	
		
		function repRiwayatAktif($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,u.namaunit,t.tipepeg,
					ab.namastatusaktif as namastatusaktifbaru,al.namastatusaktif as namastatusaktiflama
					from ".static::schema()."pe_rwtaktif r 
					left join ".static::schema()."ms_pegawai p on p.idpegawai = r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit = p.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg = p.idtipepeg
					left join ".static::schema()."lv_statusaktif ab on ab.idstatusaktif = r.statusaktifbaru
					left join ".static::schema()."lv_statusaktif al on al.idstatusaktif = r.statusaktiflama									
					where r.isvalid = 'Y' and r.tglawal between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tglawal desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}
		
		function repSuratMutasi($conn,$r_key){
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,ua.namaunit as asalunit, ut.namaunit as tujuanunit,
					case 
						when jenismutasi='P' then 'Promosi'
						when jenismutasi='D' then 'Demosi'
						when jenismutasi='R' then 'Rotasi'
					end as jenismutasi, ja.jenispegawai as jenispegasal, jt.jenispegawai as jenispegtujuan
					from ".static::table('pe_rwtmutasi')." r 
					left join ".static::schema()."ms_pegawai p on p.idpegawai = r.idpegawai
					left join ".static::schema()."ms_unit ua on ua.idunit = r.unitasal
					left join ".static::schema()."ms_unit ut on ut.idunit = r.unittujuan
					left join ".static::schema()."ms_jenispeg ja on ja.idjenispegawai = r.jenispegasal
					left join ".static::schema()."ms_jenispeg jt on jt.idjenispegawai = r.jenispegtujuan
					where nourutmutasi=$r_key";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data = $row;
			}
			
			return $a_data;
		}
	}
?>
