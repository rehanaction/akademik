<?php
	// model pangkat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPangkat extends mModel {
		const schema = 'sdm';
		const table = 'lv_pangkat';
		const order = 'kodepangkat';
		const key = 'kodepangkat';
		const label = 'pangkat';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodepangkat, golongan||' - '||namapangkat from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>