<?php
	// model status pegawai
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStatusPeg extends mModel {
		const schema = 'sdm';
		const table = 'lv_statusaktif';
		const order = 'idstatusaktif';
		const key = 'idstatusaktif';
		const label = 'status pegawai';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idstatusaktif, namastatusaktif from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>