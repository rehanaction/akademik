<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPertanyaanKuesioner extends mModel {
		const schema = 'seminar';
		const table = 'ms_pertanyaankuesioner';
		const order = 'idpertanyaan';
		const key = 'idpertanyaan';
		const label = 'Pertanyaan Kuesioner';
		//const value = 'pertanyaan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idpertanyaan, pertanyaan from ".static::table()." where isaktif = '1' order by ".static::order;	
			//return Query::arrQuery($conn,$sql);
			return $conn->GetArray($sql);
		}
	}
?>