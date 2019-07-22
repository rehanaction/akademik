<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKategoriKuisioner extends mModel {
		const schema = 'seminar';
		const table = 'ms_kategorikuesioner';
		const order = 'idkategori';
		const key = 'idkategori';
		const label = 'Kategori Jawaban Kuesioner';
		//const value = 'pertanyaan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}

	}
?>