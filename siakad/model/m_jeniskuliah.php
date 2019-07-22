<?php
	// model propinsi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJeniskuliah extends mModel {
		const schema = 'akademik';
		const table = 'lv_jeniskuliah';
		const order = 'idjeniskuliah';
		const key = 'idjeniskuliah';
		const label = 'Jenis Perkuliahan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idjeniskuliah, namajeniskuliah,iskelompok from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		function kelompok() {
			$data = array('-1' => 'Ya', '0' => 'Tidak');
			
			return $data;
		}
		function flagKelompok($conn){
			$sql="select idjeniskuliah,namajeniskuliah from ".static::table()." where iskelompok<>0";
			
			return $conn->GetArray($sql);
		}
		
	}
?>
