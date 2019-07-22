<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mOrmawa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'organisasi_kemahasiswaan';
		const order = 'kodeorganisasi';
		const key = 'kodeorganisasi';
		const label = 'namaorganisasi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
