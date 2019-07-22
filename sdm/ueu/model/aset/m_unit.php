<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUnit extends mModel {
		const schema = 'aset';
		const table = 'ms_unit';
		const order = 'a.infoleft';
		const key = 'idunit';
		const label = 'Unit';
		
		//level unit
		function level() {
			$data = array('0' => 'Level 0', '1' => 'Level 1', '2' => 'Level 2', '3' => 'Level 3', '4' => 'Level 4', '5' => 'Level 5');
			
			return $data;
		}
		
		//parent unit
		function listQuery() {
			$sql = "select a.idunit, a.kodeunit, a.namaunit, a.namasingkat, b.kodeunit as unit, a.level 
					from ".self::table()." a left join ".self::table()." b on a.parentunit=b.idunit ";
			
			return $sql;
		}
		
		function dataQuery($key){
		    $sql = "select a.idunit, a.kodeunit, a.namaunit, a.namasingkat, b.kodeunit as unit, a.level, a.infoleft, a.inforight
		        from ".self::table()." a
		        left join ".self::table()." b on a.parentunit = b.idunit
		        where a.".static::getCondition($key);
			return $sql;
		}
		
		function getListUnit($conn){
		    $data = array();
		    $rs = $conn->Execute("select idunit,kodeunit,namaunit,level from ".self::table()." order by infoleft");
		    while($row = $rs->FetchRow()){
		        $data[$row['idunit']]['kodeunit'] = $row['kodeunit'];
		        $data[$row['idunit']]['namaunit'] = $row['namaunit'];
		        $data[$row['idunit']]['level'] = $row['level'];
		    }
		    
		    return $data;
		}
		
	}
?>
