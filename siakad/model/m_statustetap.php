<?php
	// model status tetap
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStatusTetap extends mModel {
		const schema = 'sdm';
		const table = 'ms_hubkerja';//'lv_statustetap';
		const order = 'idhubkerja';
		const key = 'idhubkerja';
		const label = 'status tetap';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idhubkerja, hubkerja from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>