<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJawabanKategoriKuis extends mModel {
		const schema = 'seminar';
		const table = 'ms_jawabankategorikuis';
		const order = 'idkategori,kodejawaban';
		const key = 'idkategori,kodejawaban';
		const label = 'Jawaban Kategori Kuis';
		
		function getjawaban($conn,$idkategori){
			return $conn->getArray("select * from ".static::table()." where idkategori = '$idkategori'");
			}

		
	}
?>
