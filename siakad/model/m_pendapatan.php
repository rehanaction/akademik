<?php
	// model pendapatan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPendapatan extends mModel {
		const schema = 'akademik';
		const table = 'lv_pendapatan';
		const order = 'kodependapatan';
		const key = 'kodependapatan';
		const label = 'pendapatan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodependapatan, namapendapatan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>