<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKonversiNilai extends mModel {
		const schema = 'akademik';
		const table = 'ak_konversinilai';
		const order = 'kodemklama';
		const key = 'idkonversi';
		const sequence = 'ak_konversinilai_idkonversi_seq';
		const label = 'Konversi Nilai';

		function getMkKonversi($conn,$nimlama){
			$sql="select kodemklama,kodemkbaru from ".static::table()." where nimlama='$nimlama'";
			
			return Query::arrQuery($conn,$sql);
		}
		/**
		 * @overide
		 * Delete data
		 * @param object $conn
		 * @param mixed $key
		 * @param boolean $msg return error no saja (false) atau array error no dan msg (true)
		 * @return mixed
		 */
		function delete($conn,$key,$msg=true) {
			$keykonv=static::getCondition($key,'thnkurikulumbaru,kodeunitbaru,kodemkbaru,nimbaru');
			$err = Query::qDelete($conn,static::table(),$keykonv);
			
			if($msg)
				return array($err,static::deleteStatus($conn));
			else
				return $err;
		}
		
		function getHasilKonversi($conn,$nimbaru){
			$sql="select * from ".static::table()." where nimbaru='$nimbaru'";
			
			return $conn->GetArray($sql);
		}
	}
?>
