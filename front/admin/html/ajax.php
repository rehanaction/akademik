<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// empty buffer
	ob_clean();
	
	$conn->debug = false;
	
	// require tambahan
	require_once(Route::getUIPath('combo'));
	
	// variabel reuqest
	$f = $_REQUEST['f'];
	$q = $_REQUEST['q'];
	
	// filtering
	if(is_array($q)) {
		for($i=0;$i<count($q);$i++)
			$q[$i] = CStr::removeSpecial($q[$i]);
	}
	else
		$q = CStr::removeSpecial($q);
	
	// function
	if($f == 'acuser') {
		require_once(Route::getModelPath('user'));
		
		$a_data = mUser::find($conn,$q,"username||' - '||userdesc",'username');
		
		echo json_encode($a_data);
	}
?>
