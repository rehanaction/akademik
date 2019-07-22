<?php
	// model barang
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisPenyusutan extends mModel {
		const schema = 'aset';
		const table = 'ms_jenispenyusutan';
		const order = 'idjenispenyusutan';
		const key = 'idjenispenyusutan';
		const label = 'Jenis Penyusutan';
	}
?>
