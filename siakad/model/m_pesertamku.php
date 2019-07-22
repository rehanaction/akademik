<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMengajar extends mModel {
		const schema = 'akademik';
		const table = 'ak_pesertamku';
		const order = 'unitmku';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,unitmku';
		const label = 'Peserta MKU';
		
		
		
	}
?>
