<?php
	// model periode wisuda
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mRatePengawas extends mModel {
		const schema = 'honorakademik';
		const table = 'ms_ratepengawasujian';
		const order = 'sistemkuliah, sks, jenispeg, nohari';
		const key = 'sistemkuliah, sks, jenispeg, nohari';
		const label = 'Setting Honor Pengawas Ujian';
		
		
		function sistemKuliah($conn) {
			require_once(Route::getModelPath('sistemkuliah'));
			
			return mSistemKuliah::getArray($conn);
		}
		function getArray($conn) {
			$sql = "select sistemkuliah||'|'||sks||'|'||jenispeg||'|'||nohari as kode, rate from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
