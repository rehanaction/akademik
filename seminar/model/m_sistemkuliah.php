<?php
	// model periode wisuda
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSistemkuliah extends mModel {
		const schema = 'akademik';
		const table = 'ak_sistem';
		const order = 'sistemkuliah';
		const key = 'sistemkuliah';
		const label = 'sistem kuliah';
		
		// mendapatkan array data
		function getArray($conn,$short=false) {
			if($short)
				$sql = "select sistemkuliah,tipeprogram from ".static::table()." order by ".static::order;
			else
				$sql = "select sistemkuliah, namasistem||' - '||tipeprogram from ".static::table()." order by ".static::order;
			// $sql = "select sistemkuliah from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		function getTipe($conn){
			$sql = "select sistemkuliah, tipeprogram from ".static::table()." order by ".static::order;
			// $sql = "select sistemkuliah from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		// status program
		function statusProgram() {
			$data = array('-1' => 'Aktif', '0' => 'Tidak Aktif');
			
			return $data;
		}
	}
?>
