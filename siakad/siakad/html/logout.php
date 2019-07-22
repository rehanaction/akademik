<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// unset session
	Modul::logOut();
	
	// redirect ke logout gate
	Route::redirect($conf['logout_path']);
?>