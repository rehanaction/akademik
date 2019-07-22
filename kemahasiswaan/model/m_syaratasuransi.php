<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSyaratasuransi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_syaratklaim';
		const order = 'kodesyaratklaim';
		const key = 'kodesyaratklaim';
		const label = 'nama syarat';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
