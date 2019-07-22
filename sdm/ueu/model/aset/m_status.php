<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStatus extends mModel {
		const schema = 'aset';
		const table = 'ms_status';
		const order = 'idstatus';
		const key = 'idstatus';
		const label = 'Status Barang';
	}
?>
