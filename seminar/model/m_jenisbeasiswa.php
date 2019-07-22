<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisbeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'jenis_beasiswa';
		const order = 'idjenisbeasiswa';
		const key = 'idjenisbeasiswa';
		const label = 'namajenisbeasiswa';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idjenisbeasiswa, namajenisbeasiswa from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
