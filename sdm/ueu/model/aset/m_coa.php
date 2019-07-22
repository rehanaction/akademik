<?php
	// model coa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mCoa extends mModel {
		const schema = 'aset';
		const table = 'ms_coa';
		const key = 'idcoa';
		const order = 'idcoa';
		const label = 'COA';
		
		//level unit
		function level() {
			$data = array('0' => 'Level 0', '1' => 'Level 1', '2' => 'Level 2', '3' => 'Level 3', '4' => 'Level 4', '5' => 'Level 5');
			
			return $data;
		}
		
		//level dan kategori parent
		function pCoa($conn,$p_key) {
			$sql = "select idparent, coalesce(level,0)+1 as level from ".self::table()." where idcoa = '$p_key'";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		//parent unit
		function listQuery() {
			$sql = "select idcoa, namacoa, idparent, level from ".self::table()."";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'level': 
				    return "level = '$key'";
			    break;
			}
		}	    
	}
?>
