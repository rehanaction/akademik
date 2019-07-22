<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTahap extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_tahap';
		const sequence = 'mw_tahap_idtahap_seq';
		const order = 'idtahap';
		const key = 'idtahap';
		const label = 'Tahap Kegiatan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idtahap, namatahap from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
