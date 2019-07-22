<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUniversitas extends mModel {
		const schema = 'pendaftaran';
		const table = 'lv_ptasal';
		const order = 'idptasal';
		const key = 'idptasal';
		const label = 'universitas';
		
		function getKota($conn){
			$sql="
				SELECT kodekota, namakota FROM akademik.ms_kota
				";
			return Query::arrQuery($conn,$sql);
		}
	}
?>