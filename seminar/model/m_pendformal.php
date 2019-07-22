<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPendFormal extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_pendformal';
		const order = 'tahunmasuk';
		const key = 'idpendformal';
		const label = 'Pendidikan Formal';
		
		function getArray($conn,$nopendaftar){
			$data = $conn->GetArray("select * from ".static::table()." where nopendaftar='".$nopendaftar."' order by ".static::order."");
			return $data;
		}
		
	}
	
	
?>
