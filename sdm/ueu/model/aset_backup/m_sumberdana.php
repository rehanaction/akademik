<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSumberDana extends mModel {
		const schema = 'aset';
		const table = 'ms_sumberdana';
		const order = 'idsumberdana';
		const key = 'idsumberdana';
		const label = 'Sumber Dana';
	}
?>
