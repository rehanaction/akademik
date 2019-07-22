<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSettingpendaftaran extends mModel{
		const schema 	= 'pendaftaran';
	    const table 	= 'ms_settingpendaftaran';
	    const order 	= 'idsetting';
	    const key 		= 'idsetting';
	    const label 	= 'Setting Pendaftaran';
    }
?>
