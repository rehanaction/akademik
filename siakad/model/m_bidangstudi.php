<?php
	// model bidang studi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBidangStudi extends mModel {
		const schema = 'akademik';
		const table = 'ak_bidangstudi';
		const order = 'kodebs';
		const key = 'kodebs';
		const label = 'bidangstudi';
		
		// mendapatkan kueri list
		/* function listQuery() {
			$sql = "select b.kodeunit, b.kodebs, b.namabs, b.namabsen from ".static::table()." b
					join gate.ms_unit u on b.kodeunit = u.kodeunit";
			
			return $sql;
		} */
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit': return "kodeunit = '$key'";
					/* global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight']; */
			}
		}
		
		// mendapatkan array data
		function getArray($conn,$kodeunit) {
			$sql = "select kodebs, namabs from ".static::table()." where kodeunit = '$kodeunit'";
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>