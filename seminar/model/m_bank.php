<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBank extends mModel {
		const schema = 'h2h';
		const table = 'ms_bank';
		const order = 'bankcode';
		const key = 'bankcode';
		const label = 'bankname';
		
		// samakan dengan setting h2h
		const rekondir = '/home/www/esademo/www/h2h/rekon/';
		const h2hurl = 'http://192.168.1.8/esademo/www/h2h/';
		
		// get value
		
		function arrQuery($conn,$manual=false) {
			$data = parent::arrQuery($conn);
			if($manual)
				$data += array('000' => 'MANUAL');
			
			return $data;
		}
		
		function getNamaBank($conn,$bankcode) {
			// manual
			if($bankcode == '000')
				return 'MANUAL';
			
			$sql = "select bankname from ".static::table()." where bankcode = ".Query::escape($bankcode);
			
			return $conn->GetOne($sql);
		}
		
		// tahap rekon
		function getRekonStep() {
			$data = array();
			$data[] = array('trans','txt');
			$data[] = array('spx','spx');
			$data[] = array('rcn','rcn');
			
			return $data;
		}
	}	
?>
