<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mProdiJalur extends mModel {
		const schema = 'pendaftaran';
		const table = 'lv_prodijalurpenerimaan';		
		const order = 'kodeunit,jalurpenerimaan';
		const key = 'kodeunit,jalurpenerimaan';
		const label = 'Setting Prodi Jalur Penerimaan';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select t.*,u.namaunit from ".self::table()." t join gate.ms_unit u on t.kodeunit=u.kodeunit";
			
			return $sql;
		}
		
		// mendapatkan data
		function getData($conn,$key) {
			if(!empty($key)) {
				$sql = static::dataQuery($key);
				$row = $conn->GetRow($sql);
				
				$row['semester'] = substr($row['periode'],-1);
				$row['tahun'] = substr($row['periode'],0,4);
				
				return $row;
			}
			else
				return array();
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'kodeunit': return "t.kodeunit = '$key'";				
				case 'jalur': return "jalurpenerimaan = '$key'";				
			}
		}
		
	}
?>
