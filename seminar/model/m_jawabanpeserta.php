<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJawabanPeserta extends mModel {
		const schema = 'seminar';
		const table = 'ms_jawabanpeserta';
		const order = 'idjawabanpeserta';
		const key = 'idjawabanpeserta';
		const label = 'Jawaban Peserta';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}

	}
?>