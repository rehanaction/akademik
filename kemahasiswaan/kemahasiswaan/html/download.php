<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// variabel request
	$r_jenis = CStr::removeSpecial($_REQUEST['type']);
	$r_id = CStr::removeSpecial($_REQUEST['id']);

	 if($r_jenis == 'prestasi') {

		require_once(Route::getModelPath('prestasi'));

		$a_data = mPrestasi::getData($conn,$r_id);
		$r_file = $a_data['fileprestasi'];

	}
	else if($r_jenis == 'pengalaman') {
		require_once(Route::getModelPath('pengalaman'));

		$a_data = mPengalaman::getData($conn,$r_id);
		$r_file = $a_data['filepengalaman'];
	}
	else if($r_jenis == 'proposal') {
		require_once(Route::getModelPath('proposal'));

		$a_data = mProposal::getData($conn,$r_id);
		$r_file = $a_data['fileproposal'];
	}
	else if($r_jenis == 'lpj') {
	 
		require_once(Route::getModelPath('lpj'));
		$a_data = mLpj::getData($conn,$r_id);
		$r_file = $a_data['filelpj'];
		 
	}
	else if($r_jenis == 'guide') {
		//die($r_jenis);
		$r_file = $r_id.'.pdf';
		$r_id = $r_id.'.pdf';
	}
	else if($r_jenis == 'userguide') {
		//die($r_jenis);
		require_once(Route::getModelPath('userguide'));

		$a_data = mUserguide::getData($conn,$r_id);
		$r_file = $a_data['fileuserguide'];

	}
	else
		exit();

	$filename = Route::getUploadedFile($r_jenis,$r_id);

	$handle = fopen($filename,'rb');
	$contents = fread($handle,filesize($filename));
	fclose($handle);

	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$ext = finfo_file($finfo,$filename);
	finfo_close($finfo);

	ob_clean();

	header("Content-Type: $ext");
	header('Content-Disposition: attachment; filename="'.$r_file.'"');

	echo $contents;
?>
