<?php
	// model program pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAkreditasi extends mModel {
		const schema = 'akademik';
		const table = 'lv_akreditasi';
		const order = 'kodeakreditasi';
		const key = 'kodeakreditasi';
		const label = 'Akreditasi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodeakreditasi, keterangan from ".static::table()." order by keterangan";
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>