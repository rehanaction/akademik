<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisRawat extends mModel {
		const schema = 'aset';
		const table = 'ms_jenisrawat';
		const order = 'jenisrawat';
		const key = 'idjenisrawat';
		const label = 'Jenis Perawatan';
	}
?>
