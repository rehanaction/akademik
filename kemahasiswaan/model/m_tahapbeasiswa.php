<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTahapbeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_tahapbeasiswa';
		const order = 'idtahapbeasiswa';
		const key = 'idtahapbeasiswa';
		const label = 'namatahap';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idtahapbeasiswa, namatahap from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
