<?php
	// model status nikah
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStatusNikah extends mModel {
		const schema = 'akademik';
		const table = 'lv_statusnikah';
		const order = 'statusnikah';
		const key = 'statusnikah';
		const label = 'status nikah';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select statusnikah, namastatus from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>