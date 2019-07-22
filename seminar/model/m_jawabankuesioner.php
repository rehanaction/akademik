<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJawabanKuesioner extends mModel {
		const schema = 'seminar';
		const table = 'ms_jawabankuesioner';
		const order = 'idjawaban';
		const key = 'idjawaban';
		const label = 'Jawaban Kuesioner';
		//const value = 'pertanyaan';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idjawaban,jawaban from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}
 /**
		 * Saln data
		 * @param object $conn
		 * @param string $periodefrom
		 * @param string $periodetox
		 * @return array
		 
		function copy($conn,$periodefrom,$periodeto) {
			// hapus dulu
			$err = Query::qDelete($conn,static::getTable(),'periode = '.Query::escape($periodeto));
			
			// baru salin
			if(!$err) {
				$fields = array('periode','nomor','pertanyaan');
				
				$record = array();
				$record['periode'] = $periodeto;
				
				$err = Query::qCopy($conn,static::getTable(),$fields,$record,'periode = '.Query::escape($periodefrom));
			}
			
			return $err;
		}
		*/
	}
?>