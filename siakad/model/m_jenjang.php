<?php
	// model jenis pegawai
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenjang extends mModel {
		const schema = 'sdm';
		const table = 'lv_jenjangpendidikan';//'lv_tipepegawai';
		const order = 'urutan';
		const key = 'idpendidikan';
		const label = 'jenjang pendidikan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idpendidikan, namapendidikan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>