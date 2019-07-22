<?php
	// model kota
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mFasilitas extends mModel {
		const schema = 'akademik';
		const table = 'ms_fasilitas';
		const order = 'kodeunit';
		const key = 'kodeunit';
		const label = 'Fasilitas'; 
		// mendapatkan kueri list
		function listQuery() {
			// $sql = "select * from v_mhslist";
			$sql = "select m.kodeunit, u.namaunit, m.luastanah, m.luaskebun, m.luasrkuliah, m.luasrlab, m.luasradministrasi, m.luasrasramamhs, m.luasraula, m.luasrkomputer,m.luasrperpus from ".static::table()." m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join gate.ms_unit up on u.kodeunitparent = up.kodeunit";
			
			return $sql;
		}		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'kodeunit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit')); 
					$row = mUnit::getData($conn,$key); 
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
	}

?>