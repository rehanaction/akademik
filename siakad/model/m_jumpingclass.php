<?php
	// model propinsi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJumpingClass extends mModel {
		const schema = 'akademik';
		const table = 'ms_jumpingclass';
		const order = 'kodejumping';
		const key = 'kodejumping';
		const label = 'Kriteria Jumping Class';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodejumping, namajumping from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
