<?php
	// model jenis pegawai
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisPeg extends mModel {
		const schema = 'sdm';
		const table = 'ms_tipepeg';//'lv_tipepegawai';
		const order = 'idtipepeg';
		const key = 'idtipepeg';
		const label = 'jenis pegawai';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idtipepeg, tipepeg from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>