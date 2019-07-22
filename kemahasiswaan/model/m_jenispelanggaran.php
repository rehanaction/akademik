<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenispelanggaran extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_jenispelanggaran';
		const order = 'idjenispelanggaran';
		const key = 'idjenispelanggaran';
		const label = 'Jenis Pelanggaran';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idjenispelanggaran, namajenispelanggaran from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
