<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('riwayat'));	
	require_once(Route::getModelPath('rekrutmen'));	
	
	$tglsekarang = date('Y-m-d');
	mRiwayat::cronStruktural($conn,$tglsekarang);
	mRekrutmen::cronClose($conn,$tglsekarang);

	mRiwayat::cronEfektif($conn);
	
?>