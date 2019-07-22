<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAlasanSeminar extends mModel {
		const schema = 'seminar';
		const table = 'ms_alasan';
		const order = 'idalasan';
		const key = 'idalasan';
		const label = 'Alasan Seminar';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}

		// mendapatkan alasan
		function getAlasan($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;	
			return $conn->getArray($sql);

		}
 
	}
?>