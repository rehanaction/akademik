<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mGelombangDaftar extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_gelombangdaftar';
		const order = 'periodedaftar';
		const key = 'jalurpenerimaan,periodedaftar,idgelombang';
		const label = 'jalur penerimaan';
		
	}
?>