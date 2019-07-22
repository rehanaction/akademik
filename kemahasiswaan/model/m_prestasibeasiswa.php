<?php
	// model pendapatan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPrestasiBeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_prestasibeasiswa';
		const key = 'idprestasibeasiswa';
		const sequence = 'mw_prestasibeasiswa_idprestasibeasiswa_seq';
		const label = 'penghargaan';
	}
?>