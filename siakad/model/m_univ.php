<?php
	// model jenis pegawai
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUniv extends mModel {
		const schema = 'sdm';
		const table = 'ms_pt';//'lv_tipepegawai';
		const order = 'kodept';
		const key = 'kodept';
		const label = 'Universitas';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodept, namapt from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>