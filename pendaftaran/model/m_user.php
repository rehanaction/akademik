<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUser extends mModel {
		const schema = 'gate';
		const table = 'sc_user';
		const sequence = 'sc_user_userid_seq';
		const order = 'username, userdesc';
		const key = 'userid';
		const label = 'user';
		
		// mendapatkan role, default
		function getRole($conn,$username) {
			$sql = "select r.koderole from ".static::table('sc_userrole')." r
					join ".static::table()." u on r.userid = u.userid
					where u.username = '$username' order by r.koderole";
			$role = $conn->GetOne($sql);
			
			return $role[0];
		}
	}
?>