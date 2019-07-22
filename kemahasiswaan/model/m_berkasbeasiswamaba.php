<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBerkasBeasiswaMaba extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_berkasbeasiswamaba';
		const key = 'idpengajuanbeasiswa,kodesyaratbeasiswa,idbeasiswa';
		const label = 'berkas syarat';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
