<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSettingProdi extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_settingprodi';
		const order = 'kodeunit,sistemkuliah';
		const key = 'kodeunit,sistemkuliah';
		const label = 'Setting buka tutup prodi';
		
	}
?>
