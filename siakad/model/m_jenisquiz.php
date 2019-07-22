<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisQuiz extends mModel {
		const schema = 'akademik';
		const table = 'ms_jenisquiz';
		const order = 'idjenissoal';
		const key = 'idjenissoal';
		const label = 'Jenis Soal Quisioner';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()."order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		function getArrayCombo($conn){
			$sql = "select idjenissoal,namajenissoal from ".static::table()." order by namajenissoal";
			return Query::arrQuery($conn,$sql);
		}
	}
?>
