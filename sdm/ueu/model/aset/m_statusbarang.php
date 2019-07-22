<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStatusBarang extends mModel {
		const schema = 'aset';
		const table = 'ms_statusbarang';
		const order = 'idstatusbarang';
		const key = 'idstatusbarang';
		const label = 'Status Barang';
	}
?>
