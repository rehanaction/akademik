<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKuisionerFront extends mModel {
		const schema = 'seminar';
		const table = 'ms_jawabanpeserta';
		const order = 'idjawabanpeserta';
		const key = 'idjawabanpeserta';
		const label = 'Kuisioner Seminar';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}

		// mendapatkan array data
		function getKuisioner($conn,$idseminar,$nopendaftar) {
			$sql = "select 1 as isfilled  from ".static::table()." 
					where idseminar = '$idseminar' and nopendaftar = '$nopendaftar' 
					order by ".static::order;			
			
			return $conn->GetArray($sql);
		}

		// mendapatkan array data
		function getRekapKuisioner($conn,$idseminar) {
			$sql = "select *  from ".static::table()." 
					where idseminar = '$idseminar' and nopendaftar = '$nopendaftar' 
					order by ".static::order;
					print_r($sql);die();
			
			return $conn->GetArray($sql);
		}
 
	}
?>