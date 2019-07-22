<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisSeminar extends mModel {
		const schema = 'seminar';
		const table = 'lv_jenisseminar';
		const order = 'kodejenisseminar';
		const key = 'kodejenisseminar';
		const label = 'Jenis Seminar';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodejenisseminar, namajenisseminar from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}
 
	}
?>