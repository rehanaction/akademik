<?php
	// model program pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mProgrampendidikan extends mModel {
		const schema = 'akademik';
		const table = 'ms_programpend';
		const order = 'programpend';
		const key = 'programpend';
		const label = 'program pendidikan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select programpend from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>