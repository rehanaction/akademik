<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPendidikan extends mModel {
		const schema = 'akademik';
		const table = 'lv_pendidikan';
		const order = 'kodependidikan::int';
		const key = 'kodependidikan';
		const label = 'pendidikan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodependidikan, namapendidikan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>