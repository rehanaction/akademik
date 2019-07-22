<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAgama extends mModel {
		const schema = 'akademik';
		const table = 'lv_agama';
		const order = 'kodeagama';
		const key = 'kodeagama';
		const label = 'agama';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodeagama, namaagama from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>