<?php
	// model modul
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['model_dir'].'m_model.php');
	
	class mModul extends mModel {
		const schema = 'gate';
		const table = 'sc_modul';
		const order = 'kodemodul';
		const key = 'kodemodul';
		const label = 'modul';
	}
?>