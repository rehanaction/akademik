<?php
	// model jenis pegawai
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mIdunit extends mModel {
		const schema = 'sdm';
		const table = 'ms_unit';//'lv_tipepegawai';
		const key = 'idunit';
		const label = 'Unit';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idunit, namaunit from ".static::table()."  ";
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>