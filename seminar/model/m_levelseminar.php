<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mLevelSeminar extends mModel {
		const schema = 'seminar';
		const table = 'ms_levelseminar';
		const order = 'idlevel';
		const key = 'idlevel';
		const label = 'Level Seminar';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}
 
	}
?>