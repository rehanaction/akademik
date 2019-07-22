<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPeriode extends mModel {
		const schema = 'pendaftaran';
		const table = 'lv_syaratperjalur';
		const order = 'idsyaratjalur';
		const key = 'idsyaratjalur';
		const label = 'persyaratan';
	}
?>