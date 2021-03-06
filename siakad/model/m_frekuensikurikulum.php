<?php
	// model program pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mFrekuensikurikulum extends mModel {
		const schema = 'akademik';
		const table = 'lv_frekuensikurikulum';
		const order = 'frekuensikurikulum';
		const key = 'frekuensikurikulum';
		const label = 'Frekuensi Kurikulum';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select frekuensikurikulum, keterangan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>