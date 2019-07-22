<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTranslog extends mModel {
		const schema = 'h2h';
		const table = 'translog';
		const order = 'idtranslog desc';
		const key = 'idtranslog';
		const label = 'idtranslog';
		
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