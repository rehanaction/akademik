<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSeminar extends mModel {
		const schema = 'seminar';
		const table = 'ms_seminar';
		const order = 'idseminar';
		const key = 'idseminar';
		const value = 'namaseminar';
		const label = 'Jadwal Seminar';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select *
					from ".static::table()." b
					left join ".static::table('lv_jenisseminar')." j on b.kodejenisseminar = j.kodejenisseminar";
			
			return $sql;
		}

		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idseminar , namaseminar from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$nim = "select Count(nopeserta) 
					from   seminar.ms_peserta p 
					       left join seminar.ms_pendaftar pe 
					              on p.nopendaftar = pe.nopendaftar 
					where  nim is not null and p.idseminar = b.idseminar" ;

			$nip = "select Count(nopeserta) 
					from   seminar.ms_peserta p 
					       left join seminar.ms_pendaftar pe 
					              on p.nopendaftar = pe.nopendaftar 
					where  nip is not null and p.idseminar = b.idseminar" ;

			$umum = "select Count(nopeserta) 
					from   seminar.ms_peserta p 
					       left join seminar.ms_pendaftar pe 
					              on p.nopendaftar = pe.nopendaftar 
					where  nip is null and nim is null and p.idseminar = b.idseminar" ;

			$sql = "select b.* ,(".$nim.") as jmlnim ,(".$nip.") as jmlnip ,(".$umum.") as jmlumum, j.namajenisseminar,
					l.levelseminar as namalevelseminar, p.namapenyelenggara, s.parentkodekegiatan as parentkegiatan
					from ".static::table()." b
					left join ".static::table('lv_jenisseminar')." j  using (kodejenisseminar)
					left join ".static::table('ms_levelseminar')." l  on l.idlevel::varchar = b.levelseminar
					left join ".static::table('ms_penyelenggara')." p  on p.idpenyelenggara = b.typepengaju
					left join kemahasiswaan.ms_strukturkegiatan s on b.kodekegiatan = s.kodekegiatan
					where b.".static::getCondition($key);

			return $sql;
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
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'jadwal':
					$info['table'] = 'sm_jadwalseminar';
					$info['key'] = 'idjadwalseminar';
					$info['label'] = 'Jadwal Seminar';
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
					       js.koderuang, 
					       js.jammulai, 
					       js.jamselesai 
					from   seminar.ms_seminar s 
					       left join seminar.sm_jadwalseminar js 
					              on js.idseminar = s.idseminar
					where  s.".static::getCondition($key);
			
			//return Query::arrQuery($conn,$sql);
			return static::getDetail($conn,$sql); // getDetail me-return isi table

		}
		
		// mendapatkan nopeserta baru
		function getNoPesertaBaru($conn,$idseminar) {
			// ambil periode
			$sql = "select periode from ".static::table()." where idseminar = ".Query::escape($idseminar);
			$periode = $conn->GetOne($sql);
			
			$prefix = str_pad($periode,5,'0',STR_PAD_LEFT);
			$length = strlen($prefix);
			
			$sql = "select max(substr(nopeserta,".($length+1).")::int)
					from ".static::table('ms_peserta')."
					where substr(nopeserta,1,$length) = ".Query::escape($prefix);
			$max = $conn->GetOne($sql);
			
			return $prefix.str_pad((int)$max+1,5,'0',STR_PAD_LEFT);
		}
		
		// mendapatkan idtagihan baru
		function getIDTagihanBaru($conn,$nopeserta) {
			$prefix = $nopeserta;
			$length = strlen($prefix);
			
			$sql = "select max(substr(idtagihan,".($length+1).")::int)
					from ".static::table('sm_tagihan')."
					where substr(idtagihan,1,$length) = ".Query::escape($prefix);
			$max = $conn->GetOne($sql);
			
			return $prefix.str_pad((int)$max+1,2,'0',STR_PAD_LEFT);
		}

		// mendapatkan jadwal seminar
		function getDataJadwal($conn,$tgl='',$ruang=''){
			//$sql = "select * from ".static::table() ." where tglkegiatan = '$tgl' and koderuang = '$ruang'";	
			//$sql = "select * from ".static::table() ." where tglkegiatan = '$tgl'";	
			$sql = "select * from ".static::table();	
			
			$data = $conn->GetArray($sql);
			return $data;
		}

		// mendapatkan crah
		function getJadwalCrash($conn,$tgl='',$ruang='',$r_key =''){
			//$sql = "select * from ".static::table() ." where tglkegiatan = '$tgl' and koderuang = '$ruang' and idseminar != '$r_key'";
			$sql = "select * from ".static::table() ." where tglkegiatan = '$tgl' ";
			
			$data = $conn->GetArray($sql);
			return $data;
		}


		// get data untuk keperluan rekap seminar
		// author khusnul
		function getTampilRekap($conn,$tglawaldaftar='',$tglakhirdaftar='',$periode='',$jenisseminar='') {
			$sql = "select s.namaseminar, 
				       s.tglkegiatan,
				       s.koderuang,
				       s.tarifseminar,
				       s.cp,
				       s.keterangan,
				       s.periode,
				       s.tglawaldaftar,
				       s.tglakhirdaftar,
				       count(p.nopeserta) as jumlahpeserta,
				       count(p.waktucheckin) as hadir
					from   seminar.ms_seminar s 
					left join seminar.ms_peserta p on p.idseminar = s.idseminar";

					if (empty($tglawaldaftar) || empty($tglakhirdaftar)) {
						$sql.=" where s.periode = '$periode'";
					} else {
						$sql.=" where s.tglkegiatan between '$tglawaldaftar' and '$tglakhirdaftar' 
						and s.periode = '$periode'";
					}

					$sql.=" and s.kodejenisseminar='$jenisseminar' group by s.namaseminar,s.tglkegiatan, s.koderuang, s.tarifseminar, s.cp, s.keterangan, s.periode, tglawaldaftar, s.tglakhirdaftar
						order by s.tglkegiatan desc";


			return $conn->GetArray($sql);			
		}

		// get data untuk keperluan rekap seminar berbayar / gratis
		function getGratisBayar($conn,$peserta='',$jenis='',$periode='') {
			if ($peserta == 'M') {
				$peserta = 's.tarifseminarm' ;
			} else if ($peserta == 'P') {
				$peserta = 's.tarifseminarp' ;
			} else {
				$peserta = 's.tarifseminaru' ;
			} 

			if ($jenis == '0') {
				$jenis = '<>' ;
			} else {
				$jenis = '=' ;
			}

			$sql = "select
					   s.namaseminar, 
				       s.tglkegiatan,
					   s.jammulai,
					   s.jamselesai,
					   s.koderuang,
					   s.tglawaldaftar,
				       s.tglakhirdaftar,
				       s.tarifseminar,
				       s.cp,
					   s.periode,
					   s.batasbayar,
				       ".$peserta." as tarif
					from   seminar.ms_seminar s 
					left join seminar.ms_peserta p on p.idseminar = s.idseminar
					where coalesce($peserta,0) $jenis 0 and s.periode='$periode'
 					group by s.namaseminar,s.tglkegiatan, s.jammulai, s.jamselesai, s.tglawaldaftar, s.tglakhirdaftar, s.koderuang,s.tarifseminar, s.cp, s.batasbayar, s.periode , $peserta
					order by s.tglkegiatan desc";


			return $conn->GetArray($sql);			
		}
	}
?>
