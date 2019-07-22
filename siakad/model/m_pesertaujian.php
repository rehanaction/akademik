<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPesertaUjian extends mModel {
		const schema = 'akademik';
		const table = 'ak_pesertaujian';
		const order = 'nim,idjadwalujian';
		const key = 'nim,idjadwalujian';
		const label = 'Peserta Ujian';
		
		
	}
?>
