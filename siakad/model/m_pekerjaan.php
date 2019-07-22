<?php
	// model pekerjaan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPekerjaan extends mModel {
		const schema = 'akademik';
		const table = 'lv_pekerjaan';
		const order = 'kodepekerjaan';
		const key = 'kodepekerjaan';
		const label = 'pekerjaan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodepekerjaan, namapekerjaan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>