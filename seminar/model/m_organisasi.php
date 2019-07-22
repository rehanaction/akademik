<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mOrganisasi extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_organisasi';
		const order = 'tahun';
		const key = 'idorganisasi';
		const label = 'Data Organisasi';
		
		function getArray($conn,$nopendaftar){
			$data = $conn->GetArray("select * from ".static::table()." where nopendaftar='".$nopendaftar."' order by ".static::order."");
			return $data;
		}
		
	}
	
	
?>
