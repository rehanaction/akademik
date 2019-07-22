<?php
	// model jenis supplier
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisSupplier extends mModel {
		const schema = 'aset';
		const table = 'ms_jenissupplier';
		const order = 'jenissupplier';
		const key = 'idjenissupplier';
		const label = 'Jenis Supplier';
	}
?>
