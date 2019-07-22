<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKelompokKelas extends mModel {
		const schema = 'akademik';
		const table = 'ak_kelompokkelas';
		const order = 'idjeniskuliah,kelompok';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,idjeniskuliah';
		const label = 'Kelompok Kelas';
		
		
	}
?>
