<?php
	// model periode wisuda
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSistemkuliah extends mModel {
		const schema = 'akademik';
		const table = 'ak_sistem';
		const order = 'sistemkuliah';
		const key = 'sistemkuliah';
		const label = 'sistem kuliah';
		
		// mendapatkan array data
		function getArray($conn,$short=false) {
			$basis = modul::getBasis();
			$kampus = modul::getKampus();
			if($short){
				$sql = "select sistemkuliah,tipeprogram from ".static::table()." where 1=1 ";
				if(!empty($basis))
					$sql .= " and kodebasis = '$basis' ";
				if(!empty($kampus))
					$sql .= " and kodekampus = '$kampus' ";
				$sql .= " order by ".static::order;
			}else{
				$sql = "select sistemkuliah, namasistem||' - '||tipeprogram from ".static::table()." where 1=1 ";
				if(!empty($basis))
					$sql .= " and kodebasis = '$basis' ";
				if(!empty($kampus))
					$sql .= " and kodekampus = '$kampus' ";
				// $sql = "select sistemkuliah from ".static::table()." order by ".static::order;
				$sql .= " order by ".static::order;
			}
			return Query::arrQuery($conn,$sql);
		}
		function getTipe($conn){
			$sql = "select sistemkuliah, tipeprogram from ".static::table()." order by ".static::order;
			// $sql = "select sistemkuliah from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		// status program
		function statusProgram() {
			$data = array('-1' => 'Aktif', '0' => 'Tidak Aktif');
			
			return $data;
		}
		
		//mendapatkan sistem kuliah by basis dan kampus
		function getIdByBasisKampus($conn,$kodebasis=null,$kodekampus=null){
			$sql = "select sistemkuliah from ".static::table()." where 1=1 ";
			if(!empty($kodebasis))
				$sql .= " and kodebasis = '$kodebasis' ";
			if(!empty($kodekampus))
				$sql .= " and kodekampus = '$kodekampus' ";
			return Query::arrQuery($conn,$sql);
		}
	}
?>
