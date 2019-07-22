<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStockAset extends mModel {
		const schema = 'aset';
		const table = 'as_stockaset';
		const order = 'idstockaset';
		const key = 'idstockaset';
		const label = 'Stock Aset';
	}
?>
