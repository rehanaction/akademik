<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSumberBeasiswa extends mModel {
		const schema = 'akademik';
		const table = 'ms_sumberbeasiswa';
		const order = 'kodesumber';
		const key = 'kodesumber';
		const label = 'sumber beasiswa';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select s.kodesumber, s.namasumber, s.alamat, k.namakota, s.telp, s.email, s.contactperson
					from ".static::table()." s
					left join ".static::table('ms_kota')." k on s.kodekota = k.kodekota";
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select s.*, k.kodepropinsi
					from ".static::table()." s
					left join ".static::table('ms_kota')." k on k.kodekota = s.kodekota
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodesumber, namasumber from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getArrayNama($conn) {
			$sql = "select namasumber from ".static::table()." order by namasumber";
			
			return Query::arrQuery($conn,$sql);
		}
	}
	
	class mBeasiswa extends mModel {
		const schema = 'akademik';
		const table = 'ak_beasiswa';
		const sequence = 'ak_beasiswa_idbeasiswa_seq';
		const order = 'periodeawal desc,tglawal desc';
		const key = 'idbeasiswa';
		const label = 'beasiswa';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select b.idbeasiswa, s.namasumber, b.namabeasiswa, b.periodeawal, b.periodeakhir,
					b.tglawal, b.tglakhir, b.jumlahpenerima, b.jumlahperperiode
					from ".static::table()." b
					join ".static::table('ms_sumberbeasiswa')." s on b.kodesumber = s.kodesumber";
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select b.*, substring(b.periodeawal,1,4) as tahunawal, substring(b.periodeawal,5,1) as semesterawal,
					substring(b.periodeakhir,1,4) as tahunakhir, substring(b.periodeakhir,5,1) as semesterakhir
					from ".static::table()." b
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'penerima':
					$info['table'] = 'ak_penerimabeasiswa';
					$info['key'] = 'idbeasiswa,nim';
					$info['label'] = 'penerima beasiswa';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// penerima beasiswa
		function getPenerima($conn,$key,$label='',$post='') {
			$sql = "select p.idbeasiswa, p.nim, m.nama from ".static::table('ak_penerimabeasiswa')." p
					join ".static::table('ms_mahasiswa')." m on p.nim = m.nim
					where idbeasiswa = '$key' order by nim";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// cek penerima
		function cekPenerima($conn,$nim,$key) {
			$t_err = false;
			$t_msg = '';
			
			// mengambil periode
			$sql = "select periodeawal, periodeakhir from ".static::table()." where ".static::getCondition($key);
			$a_periode = $conn->GetRow($sql);
			
			$t_periodeawal = $a_periode['periodeawal'];
			$t_periodeakhir = $a_periode['periodeakhir'];
			
			if(empty($t_periodeawal) and empty($t_periodeakhir))
				return array($t_err,$t_msg);
			
			if(empty($t_periodeawal) or empty($t_periodeawal)) {
				if(empty($t_periodeawal))
					$t_periodeawal = $t_periodeakhir;
				else
					$t_periodekhir = $t_periodeawal;
			}
			
			// cek status mahasiswa
			$sql = "select periode from ".static::table('ak_perwalian')."
					where nim = '$nim' and statusmhs <> 'A' and periode between '$t_periodeawal' and '$t_periodeakhir'";
			$t_cek = $conn->GetOne($sql);
			
			if(!empty($t_cek)) {
				$t_err = true;
				$t_msg = 'Status mahasiswa pada '.Akademik::getNamaPeriode($t_cek).' tidak aktif';
			}
			
			// cek beasiswa
			if(!$t_err) {
				$sql = "select b.kodesumber, b.namabeasiswa from ".static::table()." b
						join ".static::table('ak_penerimabeasiswa')." p on p.idbeasiswa = b.idbeasiswa and p.nim = '$nim'
						where not('$t_periodeakhir' < coalesce(b.periodeawal,b.periodeakhir) or
						'$t_periodeawal' > coalesce(b.periodeakhir,b.periodeawal))";
				$t_cek = $conn->GetRow($sql);
				
				if(!empty($t_cek['kodesumber'])) {
					$t_err = true;
					$t_msg = 'Mahasiswa sudah mendapat beasiswa '.$t_cek['namabeasiswa'].' '.$t_cek['kodesumber'];
				}
			}
			
			return array($t_err,$t_msg);
		}
		
		// mendapatkan array data
		function getArrayNama($conn) {
			$sql = "select namabeasiswa from ".static::table()." order by namabeasiswa";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// kategori beasiswa
		function getArrayKategori($conn) {
			$sql = "select kodekategori, namakategori from h2h.lv_kategoribeasiswa order by kodekategori";
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>