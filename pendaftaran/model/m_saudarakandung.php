<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSaudaraKandung extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_saudarakandung';
		const order = 'namasaudara';
		const key = 'idsaudara';
		const label = 'Saudara Kandung';
		
		function getArray($conn,$nopendaftar){
			$data = $conn->GetArray("select * from ".static::table()." where nopendaftar='".$nopendaftar."' order by ".static::order."");
			return $data;
		}
		
	}
	
	
?>
