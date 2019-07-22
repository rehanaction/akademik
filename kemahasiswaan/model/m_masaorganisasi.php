<?php
	// model masa organisasi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMasaOrganisasi extends mModel {
		const schema = 'sdm';
		const table = 'pe_organisasi';
		const order = 'namaorganisasi';
		const key = 'nourutpo';
		const label = 'masa organisasi';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select namaorganisasi, keterangan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
