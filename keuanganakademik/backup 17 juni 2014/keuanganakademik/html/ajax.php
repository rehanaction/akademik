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
	if($f == 'datamhs') {
		require_once(Route::getModelPath('akademik'));
		if($q[1]=='nim')
			$a_data = mAkademik::getDatamhs($conn,$q[0]);
		else
			$a_data = mAkademik::getDatapendaftar($conn,$q[0]);
			
		
		echo json_encode($a_data);
	}
?>