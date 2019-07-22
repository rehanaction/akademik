<?php
	// model seminar
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('seminar'));
	
	class mSeminarFront extends mSeminar {
		// daftar inputan
		function inputColumn($conn) {
			$a_input = array();
			$a_input[] = array('kolom' => 'namaseminar', 'label' => 'Tema');
			$a_input[] = array('kolom' => 'periode', 'label' => 'Periode', 'format' => array('Akademik','getNamaPeriode'));
			$a_input[] = array('kolom' => 'tglawaldaftar', 'label' => 'Awal Pendaftaran', 'format' => array('CStr','formatDateInd'));
			$a_input[] = array('kolom' => 'tglakhirdaftar', 'label' => 'Akhir Pendaftaran', 'format' => array('CStr','formatDateInd'));
			$a_input[] = array('kolom' => 'tarifseminar', 'label' => 'Tarif Seminar', 'type' => 'N');
			$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan');
			
			$a_input[] = array('kolom' => 'typepeserta', 'label' => 'Target Peserta', 'format' => array(get_class(),'getTypePeserta'));
			$a_input[] = array('kolom' => 'semester', 'label' => 'Semester');
			
			return $a_input;
		}
		
		function inputColumnJadwal($conn) {
			// include
			require_once(Route::getModelPath('ruang'));
			
			$a_input = array();
			$a_input[] = array('kolom' => 'tgljadwal', 'label' => 'Tanggal', 'format' => array('CStr','formatDateInd'));
			$a_input[] = array('kolom' => 'jammulai', 'label' => 'Waktu Mulai', 'format' => array('CStr','formatJam'));
			$a_input[] = array('kolom' => 'jamselesai', 'label' => 'Waktu Selesai', 'format' => array('CStr','formatJam'));
			$a_input[] = array('kolom' => 'koderuang', 'label' => 'Tempat', 'type' => 'S', 'option' => mRuang::arrQuery($conn));
			
			return $a_input;
		}

		function inputKuisioner($conn) {
			$a_input = array();
			$a_input[] = array('kolom' => 'idseminar', 'label' => 'idseminar');
			$a_input[] = array('kolom' => 'nopendaftar', 'label' => 'nopendaftar');
			$a_input[] = array('kolom' => 'jawaban_1', 'label' => 'Jawaban 1');
			$a_input[] = array('kolom' => 'jawaban_2', 'label' => 'Jawaban 2');
			$a_input[] = array('kolom' => 'jawaban_3', 'label' => 'Jawaban 3');
			$a_input[] = array('kolom' => 'kritik', 'label' => 'Kritik');
			$a_input[] = array('kolom' => 'saran', 'label' => 'Saran');


			
			return $a_input;
		}
		
		// mendapatkan daftar seminar
		function getNewListQuery() {
			$sql = "select s.idseminar, s.namaseminar, s.typepeserta, s.kategori, s.pembicara, s.keterangan,
					case s.typepeserta when 'M' then 'Mahasiswa' when 'P' then 'Pegawai' when 'U' then 'Umum' end as namatypepeserta ,
					s.tglkegiatan
					from ".static::table()." s
					where s.isvalid = 1";
			
			return $sql;
		}
		
		function getNewList($conn,$sql,$page=null,$limit=null) {
			// hitung dulu, jika ada page
			if(isset($page)) {
				$sqlc = "select count(1) from ($sql) a";
				$n_rows = $conn->GetOne($sqlc);
			}
			else
				$page = 1;
			
			// baru ambil data
			if(empty($limit))
				$rs = $conn->Execute($sql);
			else
				$rs = $conn->SelectLimit($sql,$limit,($page-1)*$limit);
			
			$rows = array();
			while($row = $rs->FetchRow()) {
				// preview
				$row['preview'] = substr($row['keterangan'],0,166).(strlen($row['keterangan']) > 166 ? '...' : '');
				
				// tanggal
				$row['tglblnjadwal'] = (int)substr($row['tgljadwal'],8,2).' '.Date::indoMonth(substr($row['tgljadwal'],5,2),false);
				$row['thnjadwal'] = substr($row['tgljadwal'],0,4);
				
				// waktu
				$t_jam = '';
				if(!empty($row['jammulai']))
					$t_jam .= CStr::formatJam($row['jammulai']);
				if(!empty($row['jamselesai'])) {
					if(empty($t_jam))
						$t_jam .= 's.d. ';
					else
						$t_jam .= ' - ';
					
					$t_jam .= CStr::formatJam($row['jamselesai']);
				}
				if(!empty($t_jam))
					$row['jam'] = $t_jam.' WIB';
				
				// jenis peserta
				switch($row['typepeserta']) {
					case 'M': $row['classtypepeserta'] = 'warning'; break;
					case 'P': $row['classtypepeserta'] = 'danger'; break;
					case 'U': $row['classtypepeserta'] = 'success'; break;
				}
				
				// kategori
				$row['kategori'] = implode(', ',CStr::cStrArray($row['kategori']));
				
				// gambar
				global $conf;
				
				$t_dir = __DIR__.'/../';
				$t_file = $conf['upload_dir'].'seminar/thumbnail/'.$row['idseminar'].'.jpg';
				
				if(is_readable($t_dir.$t_file))
					$row['thumbnail'] = $t_file;
				else
					$row['thumbnail'] = 'images/no-img.jpg';
				
				$rows[] = $row;
			}
			
			if(isset($n_rows))
				return array($rows,$n_rows);
			else
				return $rows;
		}
		
		// mengambil seminar terbaru
		function getListTerbaru($conn,$limit=null,$page=null) {
			$sql = static::getNewListQuery();
			$sql = "select a.* from ($sql) a
					order by a.tgljadwal desc";
			
			return static::getNewList($conn,$sql,$page,$limit);
		}
		
		// mengambil seminar minggu ini
		function getListMingguIni($conn) {
			$tglawl = date('Y-m-d');
			$tglakr = date('Y-m-d', strtotime('+7 days'));
			
			/*
			// ambil rentang tanggal
			$d = date('N');
			$tglawal = date('Ymd',mktime(0,0,0,date('n'),date('j')-$d+1));
			$tglakhir = date('Ymd',mktime(0,0,0,date('n'),date('j')+7-$d));
			
			$sql = static::getNewListQuery();
			$sql = "select a.* from ($sql) a
					where to_char(a.tgljadwal,'YYYYMMDD') between '$tglawal' and '$tglakhir'
					order by a.tgljadwal";

					print_r($sql);die();
			
			return static::getNewList($conn,$sql,null,$limit);
			*/
			$sql = "select 
					   s.idseminar,
					   s.namaseminar, 
				       s.tglkegiatan,
				       s.koderuang,
				       s.tarifseminar,
				       s.cp,
				       s.keterangan,
				       s.periode,
				       s.tglawaldaftar,
				       s.tglakhirdaftar,
				       s.jammulai,
				       s.jamselesai,
				       s.status
					from seminar.ms_seminar s 
					where 
						s.tglkegiatan between '$tglawl' and '$tglakr'
						and s.status = 'Disetujui'
					 ";

			return $conn->GetArray($sql);
		}
		
		// mencari seminar
		function getListCari($conn,$cari='',$page=1,$limit=10) {
			$cari = strtolower($cari);
			
			$sql = static::getNewListQuery();
			$sql = "select a.* from ($sql) a
					where lower(coalesce(a.namaseminar,'')||':'||coalesce(a.pembicara,'')||':'||coalesce(array_to_string(a.kategori,':',''),'')||':'||coalesce(a.keterangan,'')) like '%$cari%'
					order by a.tglkegiatan desc";
			
			return static::getNewList($conn,$sql,$page,$limit);
		}
		
		// mengambil seminar yang diikuti
		function getListByPeserta($conn,$nopendaftar,$page=1,$limit=10) {
			$sql = static::getNewListQuery();
			$sql = "select a.* from ($sql) a
					join seminar.ms_peserta b on b.idseminar = a.idseminar and b.nopendaftar = ".Query::escape($nopendaftar)."
					order by a.tgljadwal desc";
			
			return static::getNewList($conn,$sql,$page,$limit);
		}
		
		// mendapatkan sql daftar
		function getListAktifQuery() {
			$sql = "select s.idseminar, j.tgljadwal, s.namaseminar, j.jammulai, j.jamselesai,
					coalesce(r.namaruang,j.koderuang) as namaruang, s.semester, s.typepeserta,
					case s.typepeserta when 'M' then 'Mahasiswa' when 'P' then 'Pegawai' when 'U' then 'Umum' end as namatypepeserta,
					string_agg(b.namabasis, ',' order by sb.kodebasis) as namabasis, string_agg(sb.iswajib::text, ',' order by sb.kodebasis) as iswajib
					from ".static::table()." s
					join ".static::table('sm_jadwalseminar')." j on j.idseminar = s.idseminar
					left join akademik.ms_ruang r on j.koderuang = r.koderuang
					left join seminar.sm_basisseminar sb on sb.idseminar = s.idseminar
					left join akademik.lv_basis b on sb.kodebasis = b.kodebasis
					where isvalid = 1 and current_date between s.tglawaldaftar and s.tglakhirdaftar
					group by s.idseminar, j.tgljadwal, s.namaseminar, j.jammulai, j.jamselesai,
					r.namaruang, j.koderuang, s.semester, s.typepeserta, j.idjadwalseminar
					order by j.tgljadwal, j.jammulai, j.idjadwalseminar";
			
			return $sql;
		}
		

		// mendapatkan daftar jadwal seminar aktif
		function getListAktif($conn) {
			$sql = static::getListAktifQuery();
			
			return $conn->Execute($sql);
		}
		

		// mendapatkan daftar jadwal seminar peserta akan datang
		function getListAkanDatang($conn) {
			$sql = static::getListAktifQuery();
			$sql = "select a.* from ($sql) a
					join ".static::table('ms_peserta')." b on b.idseminar = a.idseminar
					and b.nopendaftar = '".Seminar::getNoPendaftar()."'
					where a.tgljadwal >= current_date";
			
			return $conn->Execute($sql);
		}
		
		//* mendapatkan Seminar yg pernah di ikuti pendaftar */
		function getListRiwayat($conn,$nopendaftar='',$aktif=0) {
			$sql = "select s.idseminar, s.tglkegiatan, s.namaseminar, s.typepeserta, s.kategori, s.pembicara, s.keterangan, s.fileposter, s.jammulai, 
					       s.jamselesai, s.koderuang, s.status, s.temaseminar,
					       pe.nopendaftar, p.nopeserta, p.isisikuis, case when p.waktubayar is null and p.waktucheckin is null then 1 else 0 end as isopen
					from   seminar.ms_peserta p 
					       join seminar.ms_seminar s using (idseminar) 
					       left join seminar.ms_pendaftar pe using (nopendaftar)  
					where  pe.nopendaftar = '$nopendaftar'";
					if ($aktif)
					$sql.=" and coalesce(isaktif,'0')<> '0'";

			return $conn->GetArray($sql);
		} 
		
		// mendapatkan jadwal seminar
		function getListJadwal($conn,$limit=10) {
			$sql = "select 
						s.idseminar,
						s.tglkegiatan ,
						s.namaseminar, 
						s.temaseminar,
						s.typepeserta, 
						s.kategori, 
						s.pembicara, 
						s.keterangan,
						s.fileposter,
						s.jammulai,
						s.jamselesai,
						s.koderuang,
						s.status ,
						s.tglawaldaftar,
						s.tglakhirdaftar
					FROM seminar.ms_seminar s WHERE s.isvalid = 1 and s.status = 'Disetujui'
					order by s.tglkegiatan desc
					limit '$limit' ";


			$data = $conn->GetArray($sql);
			return $data;
		}
		
		// mendapatkan basis seminar
		function getListBasis($conn,$idseminar) {
			$sql = "select s.*, b.namabasis from ".static::table('sm_basisseminar')." s
					join akademik.lv_basis b on s.kodebasis = b.kodebasis
					where s.idseminar = ".Query::escape($idseminar)."
					order by s.kodebasis";
			
			return $conn->Execute($sql);
		}
		
		// cek terdaftar
		function getValidDaftar($conn,$idseminar) {
			$sql = "select * from ".static::table('ms_peserta')."
					where idseminar = ".Query::escape($idseminar)."
					and nopendaftar = '".Seminar::getNoPendaftar()."'";

			$data = $conn->GetOne($sql);

			if (!empty($data)) {
				return 1;
			} else {
				return 0;
			}
		}

		// mendapatkan daftar jenis peserta
		function getTypePeserta($kode=null) {
			$data = array('M' => 'Mahasiswa', 'P' => 'Pegawai', 'U' => 'Umum');
			if(empty($kode))
				return $data;
			else
				return $data[$kode];
		}
		
		// insert record jadwal
		function insertRecordPeserta($conn,$record,$tarif=null) {
			if (empty($tarif)) {
				$record['islunas'] = -1;
			} 

			$record['nopendaftar'] = Seminar::getNoPendaftar();
			$record['nopeserta'] = static::getNoPesertaBaru($conn,$record['idseminar']);
			$record['isvalid'] = 0;

			$sql = "select rfid from akademik.ms_mahasiswa where nim = ".Query::escape($record['nopendaftar']);
			$rfid = $conn->GetOne($sql);

			$record['rfid'] = $rfid ;
			
			$err = Query::recInsert($conn,$record,static::table('ms_peserta'));
			
			return $err;
		}

		function getProdiPeserta($conn,$key=''){
			$sql = "select kodeunit from seminar.ms_seminartopeserta where idseminar = ". $key ;

			$data = $conn->GetArray($sql);
			return $data;
		}

		function getPropertiMhs($conn,$nim=''){
			$sql = "select kodeunit , semestermhs , sistemkuliah from akademik.ms_mahasiswa where nim = '". $nim."'" ;

			$data = $conn->GetArray($sql);
			return $data;
		}

		function getPembicara($conn,$idseminar=''){
			$sql = "select idpembicara from seminar.sm_pembicara where idseminar = '". $idseminar."'" ;
			return $conn->GetArray($sql);
		}

		// get sisa kuota
		public function getSisaKuota($conn,$idseminar=''){
			$sql = "select 
			       (select Count(nopeserta) 
			        from   seminar.ms_peserta p 
			               left join seminar.ms_pendaftar pe 
			                      on p.nopendaftar = pe.nopendaftar 
			        where  nim is not null 
			               and p.idseminar = b.idseminar) as jmlnim, 
			       (select Count(nopeserta) 
			        from   seminar.ms_peserta p 
			               left join seminar.ms_pendaftar pe 
			                      on p.nopendaftar = pe.nopendaftar 
			        where  nip is not null 
			               and p.idseminar = b.idseminar) as jmlnip, 
			       (select Count(nopeserta) 
			        from   seminar.ms_peserta p 
			               left join seminar.ms_pendaftar pe 
			                      on p.nopendaftar = pe.nopendaftar 
			        where  nip is null 
			               and nim is null 
			               and p.idseminar = b.idseminar) as jmlumum 
			
					from   seminar.ms_seminar b 
					where  b.idseminar = '". $idseminar."' " ;

			return $conn->GetRow($sql);
		}		
	}
?>
