<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPendNonFormal extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_pendnonformal';
		const order = 'tahun';
		const key = 'idpendnonformal';
		const label = 'Pendidikan Non Formal';
		
		function getArray($conn,$nopendaftar){
			$data = $conn->GetArray("select * from ".static::table()." where nopendaftar='".$nopendaftar."' order by ".static::order."");
			return $data;
		}
		
	}
	
	
?>
