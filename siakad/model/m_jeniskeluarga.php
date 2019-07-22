<?php
	// model jenis keluarga
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisKeluarga extends mModel {
		const schema = 'sdm';
		const table = 'lv_jeniskeluarga';
		const order = 'jeniskeluarga';
		const key = 'jeniskeluarga';
		const label = 'jenis keluarga';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select jeniskeluarga, keterangan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
