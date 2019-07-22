<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKategoriKuis extends mModel {
		const schema = 'seminar';
		const table = 'ms_kategorikuis';
		const order = 'namakategori';
		const key = 'idkategori';
		const label = 'Kategori Jawaban Kuisioner';
		const sequence = 'ms_kategorikuis_idkategori_seq';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idkategori,namakategori from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}

	}
?>
