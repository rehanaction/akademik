<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mGedung extends mModel {
		const schema = 'aset';
		const table = 'ms_gedung';
		const order = 'idgedung';
		const key = 'idgedung';
		const label = 'Gedung';
	}
?>
