<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mLoggenerate extends mModel {
		const schema = 'h2h';
		const table = 'loggenerate';
		const order = 'idloggen';
		const key = 'idloggen';
		const label = 'idloggen';
		
	// mendapatkan array data
		function getArray($conn,$data='') {
			$sql = "select * from ".static::table()." where 1=1";
			if($data)
			foreach($data as $i => $val)
				$sql .= " and ".$i." = '".$val."'";	
				
			$sql .= " order by ".static::order;
		
			return $conn->GetArray($sql);
		}
	}
?>