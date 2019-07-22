<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisprestasi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_jenisprestasi';
		const order = 'kodejenisprestasi';
		const key = 'kodejenisprestasi';
		const label = 'namajenisprestasi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
