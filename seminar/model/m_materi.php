<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMateri extends mModel {
		const schema = 'pendaftaran';
		const table = 'lv_materiujian';
		const order = 'kodemateri';
		const key = 'kodemateri';
		const label = 'materi ujian';
		
		function getMateri($conn){
			$sql="SELECT * FROM pendaftaran.lv_materiujian";
			return $conn->SelectLimit($sql);
		}
	}
?>