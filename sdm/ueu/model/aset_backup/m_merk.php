<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMerk extends mModel {
		const schema = 'aset';
		const table = 'ms_merk';
		const order = 'merk';
		const key = 'merk';
		const label = 'Merk';
	}
?>
