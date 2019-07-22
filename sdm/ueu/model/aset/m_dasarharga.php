<?php
	// model barang
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mDasarHarga extends mModel {
		const schema = 'aset';
		const table = 'ms_dasarharga';
		const order = 'iddasarharga';
		const key = 'iddasarharga';
		const label = 'Dasar Harga';
	}
?>
