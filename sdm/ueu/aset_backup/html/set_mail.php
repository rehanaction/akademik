<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = true;	
	
	// hak akses
	
	// include
	require_once(Route::getModelPath('penghapusan'));
	
	mPenghapusan::mailHapus($conn,'');
	
	
?>
