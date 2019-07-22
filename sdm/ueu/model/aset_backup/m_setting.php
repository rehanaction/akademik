<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSetting extends mModel {
		const schema = 'aset';
		const table = 'ms_setting';
		const order = 'id';
		const key = 'id';
		const label = 'setting';

		function isOpname($conn){
		    return (int)$conn->GetOne("select nilai from ".self::table()." where id = 'isopname'");
		}

	}
?>
