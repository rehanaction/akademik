<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisLokasi extends mModel {
		const schema = 'aset';
		const table = 'ms_jenislokasi';
		const order = 'jenislokasi';
		const key = 'idjenislokasi';
		const label = 'Jenis Lokasi';
	}
?>
