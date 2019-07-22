<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mSumberBeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_sumberbeasiswa';
		const order = 'kodesumberbeasiswa';
		const key = 'kodesumberbeasiswa';
		const label = 'sumber beasiswa';

		// mendapatkan kueri list
		function listQuery() {
			$sql = "select *
					from ".static::table()." s ";

			return $sql;
		}
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select s.*, k.kodepropinsi
					from ".static::table()." s
					left join akademik.ms_kota k on k.kodekota = s.kodekota
					where ".static::getCondition($key);

			return $sql;
		}

		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;

			return Query::arrQuery($conn,$sql);
		}

		function getArrayNama($conn) {
			$sql = "select namasumber from ".static::table()." order by namasumber";

			return Query::arrQuery($conn,$sql);
		}
	}

	class mBeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_beasiswa';
		const sequence = 'beasiswa_idbeasiswa_seq';
		const order = 'periode desc,idbeasiswa desc';
		const key = 'idbeasiswa';
		const label = 'beasiswa';

		// mendapatkan kueri list
		function listQuery() {
			/* $sql = "select b.*, coalesce(v.jumlah,0) as jumlah,
					j.namajenisbeasiswa  as jenis,
				    s.namasumberbeasiswa as sumber,
				    s.namasumberbeasiswa,j.namajenisbeasiswa
					from ".static::table()." b
					left join kemahasiswaan.lv_sumberbeasiswa s
						on s.kodesumberbeasiswa = b.kodesumberbeasiswa
				    left join kemahasiswaan.lv_jenisbeasiswa j
						on j.idjenisbeasiswa = b.idjenisbeasiswa
					left join kemahasiswaan.v_jumlahpenerimabeasiswa v
						on v.idbeasiswa = b.idbeasiswa"; */
			
			$sql = "select b.*,
					j.namajenisbeasiswa  as jenis,
				    s.namasumberbeasiswa as sumber,
				    s.namasumberbeasiswa,j.namajenisbeasiswa
					from ".static::table()." b
					left join kemahasiswaan.lv_sumberbeasiswa s
						on s.kodesumberbeasiswa = b.kodesumberbeasiswa
				    left join kemahasiswaan.lv_jenisbeasiswa j
						on j.idjenisbeasiswa = b.idjenisbeasiswa";

			return $sql;
		}

		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select b.*
					from ".static::table()." b
					where ".static::getCondition($key);

			return $sql;
		}
		
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data['sumber'] = 's.namasumberbeasiswa';
						
			return $data;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'sumber': return "s.namasumberbeasiswa = '$key'";
				default:
					return parent::getListFilter($col,$key);
			}
		}

		// get list beasiswa
		function getListBea($conn,$filter) {
			$sql = "select *,
					       j.namajenisbeasiswa  as jenis,
					       s.namasumberbeasiswa as sumber
					from ".static::table()." b
					       left join kemahasiswaan.lv_sumberbeasiswa s
					              on s.kodesumberbeasiswa = b.kodesumberbeasiswa
					       left join kemahasiswaan.lv_jenisbeasiswa j
					              on j.idjenisbeasiswa = b.idjenisbeasiswa
					where  ( Lower(( j.namajenisbeasiswa ) :: varchar) like '%".$filter."%'
					          or Lower(( s.namasumberbeasiswa ) :: varchar) like '%".$filter."%'
					          or Lower(( periode ) :: varchar) like '%".$filter."%'  )
					order  by idbeasiswa ";

			return $conn->GetArray($sql);
		}

		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();

			switch($detail) {
				case 'syarat':
					$info['table'] = 'lv_syaratbeasiswa';
					$info['key'] = 'idbeasiswa,kodesyaratbeasiswa';
					$info['label'] = 'syarat beasiswa';
					break;
			}

			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}

		// penerima beasiswa
		function getSyarat($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('lv_syaratbeasiswa')." p
					where idbeasiswa = '$key' order by kodesyaratbeasiswa";

			return static::getDetail($conn,$sql,$label,$post);
		}


		// mendapatkan array data
		function getArrayBeasiswaPendaftar($conn,$date=false,$idbeasiswa=null) {
			if($date){
				$tgl = date('Y-m-d');
			}

			$sql = "select idbeasiswa,namabeasiswa from ".static::table()." where ( pesertabeasiswa = 'mb' ";
			
			if($date) {
				$sql .= " and tglawaldaftar::date <= '$tgl'::date and tglakhirdaftar::date >= '$tgl'::date ";
			}
			
			$sql .= " )".(empty($idbeasiswa) ? '' : ' or idbeasiswa = '.Query::escape($idbeasiswa));
			$sql .= " order by namabeasiswa";

			return Query::arrQuery($conn,$sql);
		}

		// mendapatkan array data
		function getArrayBeasiswaMahasiswa($conn,$date=false,$idbeasiswa=null) {
			if($date){
				$tgl = date('Y-m-d');
			}

			$sql = "select idbeasiswa,namabeasiswa from ".static::table()." where ( pesertabeasiswa = 'mh' ";
			
			//if($date) {
			//	$sql .= " and tglawaldaftar::date <= '$tgl'::date and tglakhirdaftar::date >= '$tgl'::date ";
			//}
			
			$sql .= " )".(empty($idbeasiswa) ? '' : ' or idbeasiswa = '.Query::escape($idbeasiswa));
			$sql .= " order by namabeasiswa";

			return Query::arrQuery($conn,$sql);
		}

		// mendapatkan array data
		function getArrayByPeriode($conn,$periode) {
			$sql = "select b.idbeasiswa, b.periode||' - '||namajenisbeasiswa as beasiswa , b.periode
					from ".static::table()." b
					join kemahasiswaan.lv_jenisbeasiswa j  using (idjenisbeasiswa)
					order by idbeasiswa ";

			return Query::arrQuery($conn,$sql);
		}

		// kategori beasiswa
		function getArrayKategori($conn) {
			$sql = "select kodekategori, namakategori from h2h.lv_kategoribeasiswa order by kodekategori";

			return Query::arrQuery($conn,$sql);
		}
		
		// jenis penerima beasiswa
		function getArrayJenisPenerima() {
			return array('mh' => 'Mahasiswa Aktif', 'mb' => 'Mahasiswa Baru');
		}

		// mendapatkan jumlah penerima beasiswa mhs , jumlah kuotanya dan jenis penerimanaya (mhs / maba)
		function getJumlahPenerimaMhs($conn,$idbeasiswa) {
			$sql = "select count(isditerima) as jml , jumlahbeasiswa as kuota , b.pesertabeasiswa 
					from kemahasiswaan.mw_pengajuanbeasiswa p
					left join kemahasiswaan.ms_beasiswa b on b.idbeasiswa = p.idbeasiswa
					where isditerima = '-1' and p.idbeasiswa = '$idbeasiswa'
					group by b.jumlahbeasiswa , b.pesertabeasiswa ";

			return $conn->GetRow($sql);
		}

		// mendapatkan jumlah penerima beasiswa mhs , jumlah kuotanya dan jenis penerimanaya (mhs / maba)
		function getJumlahPenerimaPndf($conn,$idbeasiswa) {
			$sql = "select count(isditerima) as jml , jumlahbeasiswa as kuota , b.pesertabeasiswa 
					from kemahasiswaan.mw_pengajuanbeasiswapendaftar p
					left join kemahasiswaan.ms_beasiswa b on b.idbeasiswa = p.idbeasiswa where isditerima = '-1' and p.idbeasiswa = '$idbeasiswa' 
					group by b.jumlahbeasiswa , b.pesertabeasiswa ";

			return $conn->GetRow($sql);
		}

		// get jenis beasiswa peserta
		function getJenisPeserta($conn,$idbeasiswa) {
			$sql = "select pesertabeasiswa from kemahasiswaan.ms_beasiswa where idbeasiswa = '$idbeasiswa'";

			return $conn->GetOne($sql);
		}
	}
?>
