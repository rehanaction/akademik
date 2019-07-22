<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKoderoles extends mModel {
		const schema = 'gate';
		const table = 'sc_role';
		const order = 'koderole';
		const key = 'koderole';
		const label = 'Role';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()."order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getArrayCombo($conn){
			$sql = "select koderole, namarole from ".static::table()." order by koderole";
			return Query::arrQuery($conn,$sql);
		}
	}
?>
