<?php
	// model semua yang berhubungan dengan anak
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAnak extends mModel {
		const schema = 'sdm';
		const table = 'lv_pendidikan';
		const order = 'idpendidikan';
		const key = 'nourutanak';
		const label = 'Anak';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select r.* from ".self::table()." r ";
			
			return $sql;
		}
	}
?>