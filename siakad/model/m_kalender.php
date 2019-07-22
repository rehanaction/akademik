<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKalender extends mModel {
		const schema = 'akademik';
		const table = 'ms_kalender';
		const order = 'idkalender';
		const key = 'idkalender';
		const label = 'kalender';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) { 
				case 'periode': return "'$key' between to_char(tglmulai, 'YYYYMM') AND to_char(tglselesai, 'YYYYMM')";
			}
		}

	}
?>