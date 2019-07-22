<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mNegara extends mModel {
		const schema = 'akademik';
		const table = 'ms_negara';
		const order = 'kodenegara';
		const key = 'kodenegara';
		const label = 'Negara';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodenegara, namanegara from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
 
	}
?>