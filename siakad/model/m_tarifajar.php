<?php
	// model periode wisuda
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTarifajar extends mModel {
		const schema = 'akademik';
		const table = 'ms_settarifajar';
		const order = 'sistemkuliah,nohari';
		const key = 'sistemkuliah , nohari , jeniskuliah ,isonline';
		const label = 'Setting Tarif Pengajaran';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select sistemkuliah||'|'||nohari||'|'||jeniskuliah||'|'||isonline as kode, nkelipatan,nkelipatansks from ".static::table()." order by ".static::order;
			$data=$conn->GetArray($sql);
			$arr=array();
			foreach($data as $row){
				$arr[$row['kode']]=array('nilaigaji'=>$row['nkelipatan'],'nilaisks'=>$row['nkelipatansks']);
			}
			return $arr;
		}
		function sistemKuliah($conn) {
			require_once(Route::getModelPath('sistemkuliah'));
			
			return mSistemKuliah::getArray($conn);
		}
		
	}
?>
