<?php
	// model jenis pegawai
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisPeg2 extends mModel {
		const schema = 'sdm';
		const table = 'ms_jenispeg';//'lv_tipepegawai';
		const order = 'idjenispegawai';
		const key = 'idjenispegawai';
		const label = 'jenis pegawai';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idjenispegawai, jenispegawai from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>