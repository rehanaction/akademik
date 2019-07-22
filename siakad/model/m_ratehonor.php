<?php
	// model periode wisuda
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mRateHonor extends mModel {
		const schema = 'honorakademik';
		const table = 'ms_ratehonor';
		const order = 'kdjnshonor';
		const key = 'kdjnshonor ,sistemkuliah';
		const label = 'Setting Tarif Honor';
		
		
		function sistemKuliah($conn) {
			require_once(Route::getModelPath('sistemkuliah'));
			
			return mSistemKuliah::getArray($conn);
		}
		function getArray($conn) {
			$sql = "select kdjnshonor||'|'||sistemkuliah as kode, rate from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getJenisRate($conn,$jenis) {
			$sql = "select s.namasistem||' '||tipeprogram, j.rate from 
					".static::table()." j
					join akademik.ak_sistem s on s.sistemkuliah=j.sistemkuliah
					where kdjnshonor='$jenis' order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
