<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPonpes extends mModel {
		const schema = 'pendaftaran';
		const table = 'lv_ponpes';
		const order = 'idponpes';
		const key = 'idponpes';
		const label = 'pondok pesantren';
		
		function getKota($conn){
			$sql="
				SELECT kodekota, namakota FROM akademik.ms_kota
				";
			return Query::arrQuery($conn,$sql);
		}
	}
?>