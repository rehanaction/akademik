<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSatuan extends mModel {
		const schema = 'aset';
		const table = 'ms_satuan';
		const order = 'idsatuan';
		const key = 'idsatuan';
		const label = 'Satuan';
	}
?>
