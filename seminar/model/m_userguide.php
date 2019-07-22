<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUserGuide extends mModel {
		const schema = 'seminar';
		const table = 'ms_guide';
		const order = 'idguide';
		const key = 'idguide';
		const label = 'User Guide';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idguide, namaguide, fileguide, isfront from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}

		function getFront($conn) {
			$sql = "select isfront from ".static::table();			
			return Query::arrQuery($conn,$sql);
		}

		function getUserGuide($conn,$status){
			$sql = "select idguide from ".static::table()." where COALESCE(isfront,0) = '".$status."' order by ".static::order." ASC LIMIT 1";	
			return $conn->GetArray($sql);
		}
 
	}
?>