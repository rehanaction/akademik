<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mCabang extends mModel {
		const schema = 'aset';
		const table = 'ms_cabang';
		const order = 'idcabang';
		const key = 'idcabang';
		const label = 'Cabang';
	}
?>
