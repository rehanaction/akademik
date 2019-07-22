<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSettingMhs extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_setting';
		const order = 'idsetting';
		const key = 'idsetting';
		const label = 'setting kemahasiswaan';
	}
?>
