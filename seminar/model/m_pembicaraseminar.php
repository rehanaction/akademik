<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPembicaraSeminar extends mModel {
		const schema = 'seminar';
		const table = 'sm_pembicara';
		const label = 'Pembicara Seminar';
		const order = 'idseminar';
		const key = '';

		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;			
			return $conn->getArray($sql);
		}

		function deletePembicara($conn,$key) {
			return Query::qDelete($conn,static::table(),"idseminar = ".Query::escape($key));
		}

		function getPembicara($conn,$key) {
			$sql = "select * from ".static::table()." where idseminar = ".$key;		
			return $conn->getOne($sql);
		}

		function getPembicaraP($conn,$key) {
			$sql = "select idpembicara from ".static::table()." where idseminar = ".$key." and jenispembicara = 'P'";		
			return $conn->getArray($sql);
		}

		function getPembicaraM($conn,$key) {
			$sql = "select idpembicara from ".static::table()." where idseminar = ".$key." and jenispembicara = 'M'";		
			return $conn->getArray($sql);
		}

		function getPembicaraU($conn,$key) {
			$sql = "select idpembicara from ".static::table()." where idseminar = ".$key." and jenispembicara = 'U'";		
			return $conn->getArray($sql);
		}

		function getPembicaraUp($conn,$key) {
			$sql = "select filettd from ".static::table()." where idseminar = ".$key."";		
			return $conn->getArray($sql);
		}
	}
?>