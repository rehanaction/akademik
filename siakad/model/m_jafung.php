<?php
	// model program pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJafung extends mModel {
		const schema = 'sdm';
		const table = 'ms_fungsional';
		const order = 'idjfungsional';
		const key = 'idjfungsional';
		const label = 'Jabatan Fungsional';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()."";
			
			return Query::arrQuery($conn,$sql);
		}

		function getArrayCombo($conn){
			$sql = "select idjfungsional,keterangan from ".static::table()."";
			return Query::arrQuery($conn,$sql);
		}
	}
?>
