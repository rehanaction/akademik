<?php
	// model pekerjaan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisPenghargaan extends mModel {
		const schema = 'akademik';
		const table = 'lv_jenispenghargaan';
		const order = 'idjenispenghargaan';
		const key = 'idjenispenghargaan';
		const label = 'jenis penghargaan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idjenispenghargaan,namapenghargaan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan array data
		function getArrayEng($conn) {
			$sql = "select idjenispenghargaan,namapenghargaanenglish from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
