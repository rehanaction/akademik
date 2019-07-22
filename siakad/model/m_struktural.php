<?php
	// model jabatan struktural
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStruktural extends mModel {
		const schema = 'kepegawaian';
		const table = 'lv_jstruktural';
		const order = 'jabatanstruktural';
		const key = 'jabatanstruktural';
		const label = 'jabatan struktural';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select jabatanstruktural from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>