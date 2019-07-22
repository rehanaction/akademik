<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	$jenis = $_GET['jenis'];
	
	// include
	require_once(Route::getModelPath('tagihan'));
	
	// ambil periode sekarang
	$r_periode = Akademik::getPeriode();
	$default = mTagihan::dendaDefault;
	
	// berikan denda
	$conn->BeginTrans();
		
	// filter
	$where = array();
	$where['periodetagihan'] = $r_periode;
	
	if(empty($jenis)) {
		// tagihan terlambat
		$err = mTagihan::deleteDenda($conn,$where);
		if(!$err)
			$err = mTagihan::updateDenda($conn,$where);
	}
	else if($jenis != $default) {
		// tagihan denda
		$where['jenistagihan'] = $jenis;
			
		$err = mTagihan::delete($conn,$where);
		if(!$err)
			$err = mTagihan::deletetagihanawal($conn,$where);
		if(!$err)
			$err = mTagihan::generateTagihanDenda($conn,$where);
	}
	
	$ok = Query::isOK($err);
	
	$conn->CommitTrans($ok);
?>
