<?php
	// model jabata fungsional
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mFungsional extends mModel {
		const schema = 'sdm';
		const table = 'lv_jfungsional';
		const order = 'jabatanfungsional';
		const key = 'jabatanfungsional';
		const label = 'jabatan fungsional';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select jabatanfungsional from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>