<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPendidikanBeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_pendidikanbeasiswamaba';
		const key = 'idpengajuanbeasiswa';
		const label = 'Riwayat Pendidikan';
		

	}
?>
