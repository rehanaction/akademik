<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSettingSkpi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_settingskpi';
		const order = 'idsetting';
		const key = 'idsetting';
		const label = 'Profil Prodi';
		
		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select u.kodeunit,namaunit
					from gate.ms_unit u
					left join ".static::table()." s using (kodeunit) 
					where level = 2 and isakad=-1 
					";
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *
					from ".static::table()." s 
					where idsetting= 1 ";
			
			return $sql;
		}
		
	}
?>
