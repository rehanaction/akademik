<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUnit extends mModel {
		const schema = 'gate';
		const table = 'ms_unit';
		const order = 'kodeunit';
		const key = 'kodeunit';
		const label = 'namaunit';
		
		// get value
		
		function getNamaUnit($conn,$kodeunit) {
			$sql = "select namaunit from ".static::table()." where kodeunit = ".Query::escape($kodeunit);
			
			return $conn->GetOne($sql);
		}
	}
?>
