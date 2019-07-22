<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisPenghapusan extends mModel {
		const schema = 'aset';
		const table = 'ms_jenispenghapusan';
		const order = 'idjenispenghapusan';
		const key = 'idjenispenghapusan';
		const label = 'Jenis Penghapusan';
	}
?>
