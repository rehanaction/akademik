<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPelanggaran extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_pelanggaranmhs';
		const sequence = 'pelanggaran_mahasiswa_idpelanggaran_seq';
		const order = 'periode desc,tglpelanggaran desc';
		const key = 'idpelanggaran';
		const label = 'pelanggaran';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idpelanggaran,p.periode, p.nim, m.nama, namajenispelanggaran, p.poinpelanggaran
					from ".static::table()." p 
					join akademik.ms_mahasiswa m  on p.nim = m.nim 
					join ".static::table('lv_jenispelanggaran')." jp on p.idjenispelanggaran = jp.idjenispelanggaran ";
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *, nama
					from ".static::table()." p
					join akademik.ms_mahasiswa m on p.nim = m.nim
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// mendapatkan nama mahasiswa
		function getNamaMahasiswa($conn,$nim) {
			$sql = "select nama from akademik.ms_mahasiswa where nim = '$nim'";
			
			$data = $conn->GetOne($sql);
			return $data;
		}
		
	}
?>
