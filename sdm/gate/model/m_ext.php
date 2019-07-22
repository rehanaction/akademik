<?php
	// model modul
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['model_dir'].'m_model.php');
	
	class mExt extends mModel {
		const schema = 'gate';
		const table = 'sc_extfile';
		const order = 'ext';
		const key = 'kodefile';
		const label = 'Ekstensi';
	}
?>