<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPelanggaran extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'pelanggaran_mahasiswa';
		//const sequence = 'ak_beasiswa_idbeasiswa_seq';
		const order = 'periode desc,tglpelanggaran desc';
		const key = 'idpelanggaran';
		const label = 'pelanggaran';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select *
					from ".static::table()." p ";
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *
					from ".static::table()." p
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
