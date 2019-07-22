<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mGelombang extends mModel {
		const schema = 'pendaftaran';
		const table = 'lv_gelombang';
		const order = 'idgelombang  ';
		const key = 'idgelombang';
		const label = 'gelombang';
		
		function getId($conn, $namagelombang){
			$sql="
				SELECT namagelombang, idgelombang FROM pendaftaran.lv_gelombang WHERE namagelombang='$namagelombang'
				";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$namagelombang]; 
		}
		function getGelombang($conn){
			$sql="
				SELECT idgelombang, namagelombang FROM pendaftaran.lv_gelombang
				";
			return Query::arrQuery($conn,$sql);
		}
		function getGelombang_front($conn){
			$sql="
				SELECT idgelombang, namagelombang FROM pendaftaran.lv_gelombang
				";
			return $conn->SelectLimit($sql);
		}
	}
?>