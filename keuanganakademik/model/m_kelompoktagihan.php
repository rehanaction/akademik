<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKelompokTagihan extends mModel {
		const schema = 'h2h';
		const table = 'lv_kelompoktagihan';
		const order = 'kodekelompok';
		const key = 'kodekelompok';
		const label = 'namakelompok';
		
		// get value
		
		function arrQuery($conn) {
			$sql = "select ".static::key.", ".static::key."||' - '||".static::label." as ".static::label."
					from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
