<?php
	// model program pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPelaksanaankurikulum extends mModel {
		const schema = 'akademik';
		const table = 'lv_pelaksanaankurikulum';
		const order = 'pelaksanaankurikulum';
		const key = 'pelaksanaankurikulum';
		const label = 'Pelaksanaan Kurikulum';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select pelaksanaankurikulum, keterangan from ".static::table()." order by keterangan";
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>