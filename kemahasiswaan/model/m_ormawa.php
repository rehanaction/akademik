<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mOrmawa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_organisasi';
		const order = 'kodeorganisasi';
		const key = 'kodeorganisasi';
		const label = 'namaorganisasi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *, nama
					from ".static::table()." p
					join akademik.ms_mahasiswa m on p.nim = m.nim
					where ".static::getCondition($key);
			
			return $sql;
		}
	}
?>
