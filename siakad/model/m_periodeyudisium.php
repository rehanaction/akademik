<?php
	// model periode yudisium
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPeriodeYudisium extends mModel {
		const schema = 'akademik';
		const table = 'ak_periodeyudisium';
		const order = 'idyudisium desc';
		const key = 'idyudisium';
		const label = 'periode yudisium';
		
		// mendapatkan array data
		function getArray($conn,$singkat=true) {
			$sql = "select idyudisium, idyudisium||coalesce(' ('||to_char(tglyudisium,'DD-MM-YYYY')||')','')
					from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>