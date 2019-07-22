<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));
	
	class mRuang extends mModel {
		const schema = 'akademik';
		const table = 'ms_ruang';
		const order = 'koderuang';
		const key = 'koderuang';
		const label = 'ruang kelas';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select r.* from ".self::table()." r join gate.ms_unit u on r.kodeunit = u.kodeunit";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select koderuang from ".static::table()." where isaktif=-1 order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		function getIpruang($conn,$koderuang){
			$sql="select ipruang from ".static::table()." where koderuang='$koderuang'";
			return $conn->GetOne($sql);
		}
	}
?>
