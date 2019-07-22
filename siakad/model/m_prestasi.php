<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPrestasi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_prestasimhs';
		const sequence = 'prestasi_mahasiswa_idprestasi_seq';
		const order = 'idprestasi desc,tglprestasi desc';
		const key = 'idprestasi';
		const label = 'prestasi';
		const uptype = 'prestasi';
		
	}
?>
