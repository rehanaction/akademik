<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// variabel request
	$r_jenis = CStr::removeSpecial($_REQUEST['type']);
	$r_id = CStr::removeSpecial($_REQUEST['id']);
	
	if($r_jenis == 'brosurseminar') {
		require_once(Route::getModelPath('seminar'));
		
		$a_data = mSeminar::getData($conn,$r_id);
		$r_file = $a_data['filereferensi'];
	}
	else if($r_jenis == 'ug') {
		require_once(Route::getModelPath('userguide'));
		
		$a_data = mUserGuide::getData($conn,$r_id);
		$r_file = $a_data['fileguide'];
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
