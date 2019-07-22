<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSettingh2hdetail extends mModel {
		const schema = 'h2h';
		const table = 'ke_settingdetail';
		const order = 'jenistagihan';
		const key = 'jenistagihan';
		const label = 'setting keuangan';
		
		
		function getDataSetting($conn) {
			$sql = "select * from ".static::table()." where idsetting = 1";
			$rs = $conn->Execute($sql);
			while($row = $rs->fetchRow()){
					$data[$row['jenistagihan']] = $row;
				}
			return $data;
		}
	}
?>