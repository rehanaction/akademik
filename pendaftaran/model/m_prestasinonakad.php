<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPrestasiNonAkad extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_prestasinonakad';
		const order = 'tahun';
		const key = 'idprestasinonakad';
		const label = 'Data Prestasi Non Akademik';
		
		function getArray($conn,$nopendaftar){
			$data = $conn->GetArray("select * from ".static::table()." where nopendaftar='".$nopendaftar."' order by ".static::order."");
			return $data;
		}
		
	}
	
	
?>
