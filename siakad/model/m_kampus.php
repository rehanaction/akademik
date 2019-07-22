<?php
	// model program pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKampus extends mModel {
		const schema = 'akademik';
		const table = 'lv_kampus';
		const order = 'kodekampus';
		const key = 'kodekampus';
		const label = 'kampus';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodekampus,namakampus from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
