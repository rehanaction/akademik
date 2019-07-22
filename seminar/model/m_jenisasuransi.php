<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisasuransi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'jenis_asuransi';
		const order = 'idjenisasuransi';
		const key = 'idjenisasuransi';
		const label = 'namajenisasuransi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
