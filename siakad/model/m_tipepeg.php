<?php
	// model program pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTipepeg extends mModel {
		const schema = 'sdm';
		const table = 'ms_tipepeg';
		const order = 'idtipepeg';
		const key = 'idtipepeg';
		const label = 'Tipe Pegawai';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idtipepeg,tipepeg, umurpensiun from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}

		function getArrayCombo($conn){
			$sql = "select idtipepeg,tipepeg from ".static::table()." order by ".static::order;
			return Query::arrQuery($conn,$sql);
		}
	}
?>
