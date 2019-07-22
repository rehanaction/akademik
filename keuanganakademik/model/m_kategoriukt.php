<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKategoriukt extends mModel {
		const schema = 'akademik';
		const table = 'lv_kategoriukt';
		const order = 'namakategoriukt';
		const key = 'kodekategoriukt';
		const label = 'Kategori UKT';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodekategoriukt, namakategoriukt from ".static::table()." order by ".static::order;

			return Query::arrQuery($conn,$sql);
		}
	}
?>
