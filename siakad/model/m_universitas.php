<?php
	// model universitas
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUniversitas extends mModel {
		const schema = 'akademik';
		const table = 'ms_universitas';
		const order = 'kodeuniversitas';
		const key = 'kodeuniversitas';
		const label = 'Universitas';
		const kodeindonesia = 'IDN';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodeuniversitas, namauniversitas from ".static::table()." order by ".static::order;
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan kueri list
		function listQuery() {
			// $sql = "select * from v_mhslist";
			$sql = "select m.kodeuniversitas, m.namauniversitas, m.isasing, m.kodenegara, m.kodekota, m.namakota as kota, 
					p.namapropinsi,
					u.namakota,
					n.namanegara
					from ".static::table()." m
					left join akademik.ms_negara n on n.kodenegara = m.kodenegara
					left join akademik.ms_kota u on u.kodekota = m.kodekota
					left join akademik.ms_propinsi p on p.kodepropinsi = u.kodepropinsi";
			
			return $sql;
		}
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'isasing':
					global $conn, $conf; 
					return "m.isasing = ".$key;
			}
		}
	}
?>