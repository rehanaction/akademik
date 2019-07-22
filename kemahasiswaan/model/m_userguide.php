<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mUserguide extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_userguide';
		const order = 'koderole';
		const key = 'koderole';
		const label = 'User Guide';
		const uptype = 'userguide';

		function getRole($conn){
			$sql = "select koderole, namarole from gate.sc_role";
			return Query::arrQuery($conn,$sql);
		}

	}
?>
