<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPilQuiz extends mModel {
		const schema = 'akademik';
		const table = 'ms_pilquiz';
		const order = 'pilihan';
		const key = 'idpilihan';
		const label = 'Pilihan Soal Quisioner';
		
		// mendapatkan array data
		function getArray($conn,$periode='') {
			$sql = "select * from ".static::table();
			if(!empty($periode))
				$sql.=" where periode='$periode'";
			$sql.=" order by ".static::order;
			return Query::arrQuery($conn,$sql);
		}
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periode = '$key'";
			}
		}
	}
?>
