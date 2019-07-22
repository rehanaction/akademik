<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenispeserta extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_jenispeserta';
		const order = 'kodejenispeserta';
		const key = 'kodejenispeserta';
		const label = 'namajenispeserta';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
