<?php
	// model pendapatan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPrestasiBeasiswaMaba extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_prestasibeasiswamaba';
		const key = 'idprestasibeasiswa';
		const squence = 'idprestasibeasiswa';
		const label = 'prestasi';
		
		
	}
?>
