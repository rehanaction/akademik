<?php
	// model periode wisuda
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenismatkul extends mModel {
		const schema = 'akademik';
		const table = 'lv_jenismk';
		const order = 'kodejenis';
		const key = 'kodejenis';
		const label = 'jenis matkul';
	}
?>