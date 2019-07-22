<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenisPerolehan extends mModel {
		const schema = 'aset';
		const table = 'ms_jenisperolehan';
		const order = 'idjenisperolehan';
		const key = 'idjenisperolehan';
		const label = 'Jenis Perolehan';
	}
?>
