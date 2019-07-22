<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJalurPenerimaan extends mModel {
		const schema = 'akademik';
		const table = 'lv_jalurpenerimaan';
		const order = 'jalurpenerimaan';
		const key = 'jalurpenerimaan';
		const label = 'jalur penerimaan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select jalurpenerimaan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>