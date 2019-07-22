<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSatuan extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'satuan';
		const order = 'kodesatuan';
		const key = 'kodesatuan';
		const label = 'namasatuan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
