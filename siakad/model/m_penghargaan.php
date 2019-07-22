<?php
	// model pendapatan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPenghargaan extends mModel {
		const schema = 'akademik';
		const table = 'ak_penghargaan';
		const order = 'tglpenghargaan';
		const key = 'idpenghargaan,nim';
		const label = 'penghargaan';
		
		
	}
?>
