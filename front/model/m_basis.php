<?php
	// model program pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBasis extends mModel {
		const schema = 'akademik';
		const table = 'lv_basis';
		const order = 'kodebasis';
		const key = 'kodebasis';
		const label = 'Basis';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodebasis,namabasis from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
