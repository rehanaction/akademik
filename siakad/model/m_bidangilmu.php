<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBidangilmu extends mModel {
		const schema = 'akademik';
		const table = 'lv_bidangilmu';
		const order = 'bidangilmu';
		const key = 'bidangilmu';
		const label = 'Bidang Ilmu';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select bidangilmu, namabidang from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
 
	}
?>