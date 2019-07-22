<?php
	// model status mahasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStatusMhs extends mModel {
		const schema = 'akademik';
		const table = 'lv_statusmhs';
		const order = 'statusmhs';
		const key = 'statusmhs';
		const label = 'status mahasiswa';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select statusmhs, namastatus from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>