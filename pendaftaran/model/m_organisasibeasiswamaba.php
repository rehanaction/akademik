<?php
	// model pendapatan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mOrganisasiBeasiswaMaba extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_organisasibeasiswa';
		const key = 'idorganisasibeasiswa';
		const squence = 'idorganisasibeasiswa';
		const label = 'organisasi';
		
		
	}
?>
