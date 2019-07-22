<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mDetailJadwal extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_jadwaldetail';
		const order = 'koderuang';
		const key = 'idjadwaldetail';
		const label = 'Jadwal Detail';
		
	}
?>
