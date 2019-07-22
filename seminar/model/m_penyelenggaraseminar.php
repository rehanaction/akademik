<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPenyelenggaraSeminar extends mModel {
		const schema = 'seminar';
		const table = 'ms_penyelenggara';
		const order = 'idpenyelenggara';
		const key = 'idpenyelenggara';
		const label = 'Penyelenggara Seminar';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}
 
	}
?>