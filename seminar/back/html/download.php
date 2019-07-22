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

	else if($r_jenis == 'ijinseminar') {
		require_once(Route::getModelPath('ijinseminar'));
		
		$a_data = mIjinSeminar::getData($conn,$r_id);
		$r_file = $a_data['fileijinseminar'];
	}
	else if($r_jenis == 'seminar') {
		require_once(Route::getModelPath('seminar'));
		
		$a_data = mSeminar::getData($conn,$r_id);
		$r_file = $a_data['fileposter'];
	}
	else if($r_jenis == 'brosurseminar') {
		require_once(Route::getModelPath('seminar'));
		
		$a_data = mSeminar::getData($conn,$r_id);
		$r_file = $a_data['filereferensi'];
	}
	else if($r_jenis == 'ttdseminar') {
		require_once(Route::getModelPath('seminar'));
		
		$a_data = mSeminar::getData($conn,$r_id);
		$r_file = $a_data['filettd'];
	}
	else if($r_jenis == 'tandatangan') {
		require_once(Route::getModelPath('unit'));
		
		$a_data = mUnit::getData($conn,$r_id);
		$r_file = $a_data['tandatangan'];
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
