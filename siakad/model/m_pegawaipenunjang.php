<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPegawaiPenunjang extends mModel {
		const schema = 'akademik';
		const table = 'ms_pegawaipenunjang';
		const order = 'nopegawai';
		const key = 'nopegawai';
		const label = 'Pegawai Penunjang Akademik';
		
		
		
	}
?>
