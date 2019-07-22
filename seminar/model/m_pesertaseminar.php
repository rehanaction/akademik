<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPesertaSeminar extends mModel {
		const schema = 'seminar';
		const table = 'ms_peserta';
		const key 	= 'nopeserta';
		const label = 'Peserta Seminar';
		const order = 'nopendaftar';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select p.nopeserta,p.nopendaftar,p.waktudaftar,p.waktubayar,p.waktucheckin,p.waktucheckout,
					       s.namaseminar as namaseminar , coalesce(pe.nama,m.nama) as namapeserta , s.isbuka,
					       p.islunas , pe.rfid,
					       (case 
								when(pe.nim is not null) then s.tarifseminarm
								when(pe.nip is not null) then s.tarifseminarp
								else s.tarifseminaru
							end) as tarif
					from ".static::table()." p 
					        left join seminar.ms_seminar s 
					        	on s.idseminar = p.idseminar 
					        left join seminar.ms_pendaftar pe 
					        	on pe.nopendaftar = p.nopendaftar
					        left join akademik.ms_mahasiswa m 
              					on m.nim = p.nopendaftar ";

			return $sql;
		}
		
		function getArrayListFilterCol() {
			$data['namapeserta'] = 'pe.nama';
			$data['nopendaftar'] = 'pe.nopendaftar';
			$data['lunas'] = 'p.islunas ';
			$data['tarif'] = 's.tarifseminar';
			$data['idseminar'] = 'p.idseminar';
			
			return $data;
		}
		
		function getListFilter($col,$key) {
			switch($col) {
				case 'namapeserta': 
					return "pe.nama  = '$key'";
				case 'idseminar': 
					return "p.idseminar  = '$key'";
				case 'lunas': 
					return "p.islunas  = '$key'";
				default:
					return parent::getListFilter($col,$key);
			}
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select p.* , 
					       s.namaseminar as namaseminar , pe.nama as namapeserta
					from ".static::table()." p 
					        left join seminar.ms_seminar s 
					       		on s.idseminar = p.idseminar
					        left join seminar.ms_pendaftar pe 
					        	on pe.nopendaftar = p.nopendaftar 
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'jadwal':
					$info['table'] = 'ms_peserta';
					$info['key'] = 'nopeserta';
					$info['label'] = 'Peserta Seminar';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		

		// get jadwal seminar
		function getJadwalSeminar($conn,$key) {
			$sql = "select js.idjadwalseminar, 
					       js.idseminar, 
					       js.tgljadwal, 
					       js.jammulai, 
					       js.jamselesai,
					       s.tarif 

					from   seminar.ms_seminar s 
					       left join seminar.sm_jadwalseminar js 
					              on js.idseminar = s.idseminar
					where  s.".static::getCondition($key);
			
			//return Query::arrQuery($conn,$sql);
			return static::getDetail($conn,$sql); // getDetail me-return isi table

				
		}

		// get peserta seminar
		function getPesertaSeminar($conn,$key,$nopeserta) {
			$sql = "select * , se.tarifseminar
					from   seminar.ms_peserta s 
					left join seminar.ms_seminar se on se.idseminar = s.idseminar 
					where  s.idseminar =".$key."
					and s.nopeserta ='".$nopeserta."'";

							
			//return Query::arrQuery($conn,$sql);
			return static::getDetail($conn,$sql); // getDetail me-return isi table				
		}

		function getPesertaSeminarExist($conn,$idseminar,$nopendaftar) {
			$sql = "select *
					from   seminar.ms_peserta s 
					where  s.idseminar =".$idseminar." and s.nopendaftar = '".$nopendaftar."'";

							
			//return Query::arrQuery($conn,$sql);
			return static::getDetail($conn,$sql); // getDetail me-return isi table				
		}

		function getIsMahasiswa($conn,$idseminar,$nopendaftar) {
			$sql = "select nim
					from   akademik.ms_mahasiswa  
					where  nim ='".$nopendaftar."'";
			
			return $conn->GetOne($sql);			
		}

		function getIsPendaftar($conn,$idseminar,$nopendaftar) {
			$sql = "select nopendaftar
					from   seminar.ms_pendaftar   
					where  nopendaftar ='".$nopendaftar."'";
			
			return $conn->GetOne($sql);			
		}

		// untuk mendapat status absen buka
		function getIsBukaAbsen($conn,$nopeserta) {
			$sql = "select s.isbuka from ".static::table()." p
  					left join seminar.ms_seminar s 
  						on s.idseminar = p.idseminar
  					where p.nopeserta = '".$nopeserta."'";	

			return Query::arrQuery($conn,$sql);			
		}
		
		function getDataPeserta($conn,$nopeserta=''){
			$sql = "select p.*,pd.nama, namaseminar
					from ".static::table()." p 
					join  ".static::table('ms_pendaftar')." pd using (nopendaftar)
					join ".static::table('ms_seminar')." using (idseminar)
					where p.nopeserta = '$nopeserta' ";
			
			return $conn->GetRow($sql);					
		}

				
		// mendapatkan nama mahasiswa
		function getNamaMahasiswa($conn,$nim) {
			$sql = "select nama from akademik.ms_mahasiswa where nim = '$nim'";
			
			$data = $conn->GetOne($sql);
			return $data;
		}

		// mendapatkan nama mahasiswa
		function getNamaPegawai($conn,$nik) {
			$sql = "select namadepan from sdm.ms_pegawai where nik = '$nik'";
			
			$data = $conn->GetOne($sql);
			return $data;
		}
		
		function getPeserta($conn,$key) {
			$sql = "select * , se.tarifseminar
					from   seminar.ms_peserta s 
					left join seminar.ms_seminar se on se.idseminar = s.idseminar 
					where  s.idseminar =".$key;

							
			//return Query::arrQuery($conn,$sql);
			return static::getDetail($conn,$sql); // getDetail me-return isi table				
		}

		function getListPesertaPerSeminar($conn,$idseminar=''){
			$sql = "select *
					from seminar.ms_peserta  p 
				    where p.idseminar = '$idseminar' ";
			
			return $conn->GetArray($sql);		
			
		}

		function getRfid($conn,$idseminar,$rfid,$orid=false) {
			$label = 'RFID';
			if($orid)
				$label .= ' / No Pendaftar';
			
			$sql = "select nopendaftar from seminar.ms_pendaftar where rfid = ".Query::escape($rfid);
			$idpendaftar = $conn->GetOne($sql);
			
			if(empty($idpendaftar) and $orid)
				$idpendaftar = $rfid;
			
			if(!empty($idpendaftar)) {
				$psql = "select ps.*, pd.rfid, pd.nim, pd.nip from seminar.ms_peserta ps join seminar.ms_pendaftar pd using (nopendaftar) 
						where pd.nopendaftar = ".Query::escape($idpendaftar)." and ps.idseminar = ".Query::escape($idseminar);
				$peserta = $conn->getRow($psql);
				$data['peserta']= $peserta;
			}
			
			if (empty ($peserta) or empty($peserta['rfid']))
				$msg = " Data dengan $label $rfid tidak ditemukan sebagai peserta seminar atau $label tidak dikenali";
				
			if (!empty ($rfid)){
				$seminar = $conn->getRow("select * from seminar.ms_seminar where idseminar = '$idseminar'");
				$data['seminar'] = $seminar;

				if (empty ($seminar['isbuka']))
					$msg = " Pendaftaran seminar ".$seminar['namaseminar']." tidak dibuka";
			}
			
			return array($data,$msg);
		}

		// get data untuk bukti pembayaran
		function getBuktiPembayara($conn,$nopeserta) {
			$sql = "select p.nama, 
					       sm.namaseminar, 
					       s.nopeserta, 
					       p.nopendaftar, 
					       s.waktubayar, 
					       sm.tglkegiatan, 
					       sm.tarifseminar, 
					       s.waktubayar ,
					        case
								when p.nim is not null then sm.tarifseminarm
								when p.nip is not null then sm.tarifseminarp
								when p.nip is null and p.nim is null then sm.tarifseminaru
						    end as tarif
					from   seminar.ms_peserta s 
					       left join seminar.ms_pendaftar p 
					              on p.nopendaftar = s.nopendaftar 
					       left join seminar.ms_seminar sm 
					              on sm.idseminar = s.idseminar 
					where s.nopeserta ='".$nopeserta."'";

							
			//return Query::arrQuery($conn,$sql);
			return static::getDetail($conn,$sql); // getDetail me-return isi table				
		}

		// get data untuk keperluan absensi seminar
		// author khusnul
		function getTampilAbsensi($conn,$idseminar,$periode,$nopeserta = '',$kodekampus='') {
			$sql = "select s.namaseminar, 
					       s.tglawaldaftar, 
					       s.tglakhirdaftar, 
					       s.tglkegiatan,s.koderuang,
					       s.periode, 
					       pd.nama, 
					       pd.sex, 
					       s.jammulai,
					       s.jamselesai,
					       p.*,ps.namapembicara,
					       u.namaunit, up.namaunit as fakultas,
						   p.kritik, p.saran,
						   u.kodeunit, s.idseminar,
						   k.namakampus
					from   seminar.ms_peserta p 
					       left join seminar.ms_seminar s 
					              on s.idseminar = p.idseminar
					       left join (select idseminar,idpembicara as namapembicara from seminar.sm_pembicara
					       ) ps on ps.idseminar = s.idseminar 
					       left join seminar.ms_pendaftar pd 
					              on pd.nopendaftar = p.nopendaftar
					       left join akademik.ms_mahasiswa m
					              on m.nim = pd.nim
					       left join gate.ms_unit u
					              on m.kodeunit = u.kodeunit
					       left join gate.ms_unit up
					              on up.kodeunit = u.kodeunitparent
					       left join akademik.ak_sistem sk
					       		  on sk.sistemkuliah =  m.sistemkuliah
					       left join akademik.lv_kampus k
					       		  on sk.kodekampus = k.kodekampus
					where s.idseminar ='".$idseminar."'
					and s.periode ='".$periode."'";
					if (!empty ($nopeserta))
					$sql.=" and p.nopeserta = '$nopeserta'";
					
					if (!empty ($kodekampus))
					$sql.=" and k.kodekampus = '$kodekampus'";

					$sql.=" order by k.kodekampus DESC, up.namaunit, pd.nama ASC ";

			return $conn->GetArray($sql);
			
		}

		// get data untuk keperluan daftar peserta seminar
		// author khusnul
		function getTampilPeserta($conn,$idseminar,$periode) {
			$sql = "select s.namaseminar, 
				       s.tglawaldaftar, 
				       s.tglakhirdaftar, 
				       s.periode, 
				       s.tglkegiatan,
				       pd.nama, 
				       pd.sex, 
				       pd.noktp, 
				       pd.telp, 
				       pd.hp, 
					   pd.nim,
					   pd.nip,
				       pd.email, 
				       pd.tmplahir,
				       pd.namaperusahaan,
				       pd.tgllahir,
				       pr.namapropinsi,  
				       pd.alamat,
					   k.namakota, 
				       pd.kodepos, 
				       pd.iskerja, 
					   p.*, u.namaunit, up.namaunit as fakultas
				from   seminar.ms_peserta p 
				       left join seminar.ms_seminar s 
				              on s.idseminar = p.idseminar 
				       left join seminar.ms_pendaftar pd 
				              on pd.nopendaftar = p.nopendaftar 
				       left join akademik.ms_propinsi pr 
				              on pr.kodepropinsi = pd.kodepropinsi
				       left join akademik.ms_kota k 
				              on k.kodekota = pd.kodekota 
				       left join akademik.ms_mahasiswa m
				              on m.nim = pd.nim
				       left join gate.ms_unit u
				              on m.kodeunit = u.kodeunit
				       left join gate.ms_unit up
				              on up.kodeunit = u.kodeunitparent
					where s.idseminar ='".$idseminar."'
					and s.periode ='".$periode."'";

					
			return $conn->GetArray($sql);
			
		}

		// get data untuk keperluan laporan data pembayaran seminar
		// author khusnul
		function getTampilStatusbayar($conn,$idseminar,$status) {
			$sql = "select p.nopeserta, 
					       p.nopendaftar, 
					       substr(p.waktudaftar::varchar,1,10) as waktudaftar, 
					       p.waktubayar,
                           s.batasbayar,						   
					       s.namaseminar             as namaseminar, 
					       coalesce(pe.nama, m.nama) as namapeserta, 
					       ( case 
					           when coalesce(p.islunas,0) <> 0 then 'Lunas' 
					           else 'Belum Lunas'
					         end )                   as lunas, 
					       ( case 
					           when( pe.nim is not null ) then s.tarifseminarm 
					           when( pe.nip is not null ) then s.tarifseminarp 
					           else s.tarifseminaru 
					         end )                   as tarif 
							
							from   seminar.ms_peserta p 
					        left join seminar.ms_seminar s 
					              on s.idseminar = p.idseminar 
					        left join seminar.ms_pendaftar pe 
					              on pe.nopendaftar = p.nopendaftar 
					        left join akademik.ms_mahasiswa m 
					              on m.nim = p.nopendaftar 
					    
					where s.idseminar ='".$idseminar."' " ; 
					if ($status == '-1') {
						$sql.= "and coalesce(p.islunas,0) <> 0" ;
					} else {
						$sql.= "and p.islunas is null" ;
					}

			return $conn->GetArray($sql);
		}
	}
?>
