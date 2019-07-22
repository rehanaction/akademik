<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSeminarTopeserta extends mModel {
		const schema = 'seminar';
		const table = 'ms_seminartopeserta';
		const order = 'idseminar';
		const key = 'idseminar';
		const label = 'Seminar Peserta';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			return $conn->getArray($sql);
		}

		function deleteFakultas($conn,$key) {
			return Query::qDelete($conn,static::table(),"idseminar = ".Query::escape($key));
		}
		
		// mendapatkan array data
		function getDataPeserta($conn,$idseminar) {
			$kodeunit = array();
			$sql = "select kodeunit from ".static::table()." where idseminar = '$idseminar' order by ".static::order;			
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$kodeunit[] = $row['kodeunit'];
			}
			return $kodeunit;
		}
 
	}
?>
