<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPerusahaanasuransi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_perusahaanasuransi';
		const order = 'kodeprsasuransi';
		const key = 'kodeprsasuransi';
		const label = 'Perusahaan Asuransi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
