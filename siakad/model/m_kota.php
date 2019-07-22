<?php
	// model kota
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKota extends mModel {
		const schema = 'akademik';
		const table = 'ms_kota';
		const order = 'kodekota';
		const key = 'kodekota';
		const label = 'kota';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'propinsi': return "kodepropinsi = '$key'";
			}
		}
		
		// mendapatkan array data
		function getArray($conn,$propinsi='') {
			$sql = "select kodekota, namakota from ".static::table();
			if(!empty($propinsi))
				$sql .= " where kodepropinsi = '$propinsi'";
			$sql .= " order by namakota";
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>