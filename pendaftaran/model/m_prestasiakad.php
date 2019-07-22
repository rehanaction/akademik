<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPrestasiAkad extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_prestasiakad';
		const order = 'tahun';
		const key = 'idprestasiakad';
		const label = 'Data Prestasi';
		
		function getArray($conn,$nopendaftar){
			$data = $conn->GetArray("select * from ".static::table()." where nopendaftar='".$nopendaftar."' order by ".static::order."");
			return $data;
		}
		
	}
	
	
?>
