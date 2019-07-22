<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSyaratKegiatan extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_syaratkegiatan';
		const order = 'kodesyaratkegiatan';
		const key = 'kodesyaratkegiatan';
		const label = 'nama syarat';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
