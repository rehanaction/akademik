<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSanksipelanggaran extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'sanksi_pelanggaran';
		const order = 'idsanksipelanggaran';
		const key = 'idsanksipelanggaran';
		const label = 'namasanksipelanggaran';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
