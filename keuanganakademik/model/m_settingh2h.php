<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSettingh2h extends mModel {
		const schema = 'h2h';
		const table = 'ke_setting';
		const order = 'idsetting';
		const key = 'idsetting';
		const label = 'setting keuangan';
		
		// mendapatkan data untuk session
		function getDataSession($conn) {
			$sql = "select * from ".static::table()." where idsetting = 1";
			$row = $conn->GetRow($sql);
			
			$rows = array();
			$rows['KURIKULUM'] = $row['kurikulum'];
			$rows['PERIODE'] = $row['periodesekarang'];
			$rows['TAHAP'] = $row['tahapfrs'];
			$rows['ISINILAI'] = $row['isinilai'];
			$rows['BIODATAMHS'] = $row['biodatamhs'];
			$rows['PERIODENILAI'] = $row['periodenilai'];
			
			return $rows;
		}
		
		function getDataSetting($conn) {
			$sql = "select * from ".static::table()." where idsetting = 1";
			$rs = $conn->getRow($sql);
			return $rs;
		}
		
		function getErrorcode($conn,$errorcode,$param){
				$arr_code = array();
			}
	}
?>