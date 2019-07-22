<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKetkegiatan extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_ketkegiatan';
		const squence = 'mw_ketkegiatan_idketerangan_seq';
		const order = 'idketerangan';
		const key = 'idketerangan';
		const label = 'Keterangan Kegiatan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
