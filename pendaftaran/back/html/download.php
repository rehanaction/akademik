<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// variabel request
	$r_jenis = CStr::removeSpecial($_REQUEST['type']);
	$r_id = CStr::removeSpecial($_REQUEST['id']);
	
	if($r_jenis == 'pengumumanpendaftaran') {
		require_once(Route::getModelPath('gelombangdaftar'));
		$a_data = mGelombangDaftar::getData($conn,$r_id);		
		$r_file = $a_data['filependaftaran'];
		
	} else if($r_jenis == 'pengumumandaftarulang') {
		require_once(Route::getModelPath('gelombangdaftar'));
		$a_data = mGelombangDaftar::getData($conn,$r_id);
		$r_file = $a_data['filedaftarulang'];
		
	} else if($r_jenis == 'pendaftar') {
		require_once(Route::getModelPath('pendaftar'));
		
		$a_data = mPendaftar::getData($conn,$r_id);
		$r_file = $a_data['filepindahprodi'];
	}
	else
		exit();
		
	$filename = Route::getUploadedFile($r_jenis,$r_file);
	
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
