<?php
	// model propinsi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPropinsi extends mModel {
		const schema = 'akademik';
		const table = 'ms_propinsi';
		const order = 'kodepropinsi';
		const key = 'kodepropinsi';
		const label = 'propinsi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodepropinsi, namapropinsi from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>