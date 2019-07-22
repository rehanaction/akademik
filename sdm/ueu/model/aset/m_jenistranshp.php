<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisTransHP extends mModel {
		const schema = 'aset';
		const table = 'ms_jenistranshp';
		const order = 'jenistranshp';
		const key = 'idjenistranshp';
		const label = 'Jenis Transaksi Habis Pakai';
	}
?>
