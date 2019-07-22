<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMastPelengkap extends mModel {
		const schema = 'sdm';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'propinsi': return "substring(idkabupaten,1,2) = '$key'";
				case 'kabupaten': return "substring(idkecamatan,1,4) = '$key'";
				case 'kecamatan': return "substring(idkelurahan,1,6) = '$key'";
			}
		}
	}
?>
