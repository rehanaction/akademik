<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKota extends mModel {
		const schema = 'akademik';
		const table = 'ms_kota';
		const order = 'kodekota';
		const key = 'kodekota';
		const label = 'Kota';
		
		function getPropinsi($conn,$kodekota){
			$sql="select kodepropinsi from ".static::table()." where kodekota='$kodekota'";
			$kodepropinsi=$conn->GetOne($sql);
			
				return $kodepropinsi;
			
		}
	}
	
	
?>
