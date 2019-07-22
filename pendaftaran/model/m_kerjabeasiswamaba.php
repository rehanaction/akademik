<?php
	// model pendapatan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKerjaBeasiswaMaba extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_kerjabeasiswa';
		const key = 'idkerjabeasiswa';
		const squence = 'idkerjabeasiswa';
		const label = 'Pengalaman Kerja';
		
	}
?>
