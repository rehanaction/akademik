<?php
	// model pendapatan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPelatihanBeasiswaMaba extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_pelatihanbeasiswa';
		const key = 'idpelatihanbeasiswa';
		const squence = 'idpelatihanbeasiswa';
		const label = 'pelatihan';
		
	}
?>
