<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKondisi extends mModel {
		const schema = 'aset';
		const table = 'ms_kondisi';
		const order = 'idkondisi';
		const key = 'idkondisi';
		const label = 'Kondisi Barang';
	}
?>
