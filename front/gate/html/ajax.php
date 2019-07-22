<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	if(!Modul::isAuthenticated())
		exit();
	
	// clean buffer
	ob_clean();
	
	$conn->debug = false;
	
	$c = $_REQUEST['c'];
	$p = $_REQUEST['p'];
	
	if($c == 'access') {
		list($kodemodul,$koderole,$kodeunit,$kodebasis,$kodekampus) = explode('_',CStr::removeSpecial($p));
		
		// include
		require_once(Route::getModelPath('user'));
		
		// cek hak akses akses
		$isauth = false;
		$akses = mUser::getDataAuth($conn,Modul::getUserID());
		
		foreach($akses[$kodemodul]['data'] as $t_data) {
			if($t_data['koderole'] == $koderole and $t_data['kodeunit'] == $kodeunit) {
				$isauth = true;
				break;
			}
		}
		
		// dapatkan data session SIM
		$s = Modul::setSIMSession($conn,$kodemodul,$koderole,$kodeunit,$kodebasis,$kodekampus);
		if($s === false)
			echo 0;
		else
			echo $conf[$kodemodul.'_path'].':'.rawurlencode($s);
	}
?>
