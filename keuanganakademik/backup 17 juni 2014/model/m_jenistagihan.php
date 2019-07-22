<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenistagihan extends mModel {
		const schema = 'h2h';
		const table = 'lv_jenistagihan';
		const order = 'jenistagihan';
		const key = 'jenistagihan';
		const label = 'jenistagihan';
		
	// mendapatkan array data
		function getArray($conn,$frekuensitagihan='',$sks='') {
			$sql = "select * from ".static::table()." where 1=1";
			if($frekuensitagihan)
				if(is_array($frekuensitagihan))
					$sql .= " and frekuensitagihan in ('".implode("','",$frekuensitagihan)."')";
				else
					$sql .= " and frekuensitagihan = '".$frekuensitagihan."'";
			if($sks <> '')
				$sql .= " and issks = '".$sks."'";
				
			$sql .= " order by ".static::order;
		
			return $conn->GetArray($sql);
		}
		
		// mendapatkan array data
		function getDatacombo($conn) {
			$sql = "select * from ".static::table()." where 1=1";
			$sql .= " order by ".static::order;
		
			$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()){
					
						$data[$row['jenistagihan']] = $row['jenistagihan'].' - '.$row['namajenistagihan'];
					}
					
				return $data;
		}
	}
?>