<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTingkatprestasi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_tingkatprestasi';
		const order = 'kodetingkatprestasi';
		const key = 'kodetingkatprestasi';
		const label = 'namajtingkatprestasi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
