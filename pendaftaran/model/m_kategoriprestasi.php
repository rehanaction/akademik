<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKategoriprestasi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_kategoriprestasi';
		const order = 'kodekategoriprestasi';
		const key = 'kodekategoriprestasi';
		const label = 'namajkategoriprestasi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
