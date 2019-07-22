<?php
	// model bidang pekerjaan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBidangKerja extends mModel {
		const schema = 'akademik';
		const table = 'lv_bpekerjaan';
		const order = 'bidangkerja';
		const key = 'bidangkerja';
		const label = 'bidang pekerjaan';
	}
?>