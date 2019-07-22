<?php
	// model warganegara
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mWargaNegara extends mModel {
		const schema = 'akademik';
		const table = 'lv_warganegara';
		const order = 'kodewn';
		const key = 'kodewn';
		const label = 'warganegara';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodewn, namawn from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>