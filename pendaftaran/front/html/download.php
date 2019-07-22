<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//echo $conf['upload_dir'];die();
	

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
	else if ($r_jenis == 'ktp'){
		require_once(Route::getModelPath('pendaftar'));
		
		$a_data = mPendaftar::getData($conn,$r_id);
		$r_file = $a_data['filektp'];
		$r_idfile = $r_id;
	}
	else if ($r_jenis == 'raport'){
		require_once(Route::getModelPath('pendaftar'));
		
		$a_data = mPendaftar::getData($conn,$r_id);
		$r_file = $a_data['fileraport'];
		$r_idfile = $r_id;
	}
	else if ($r_jenis == 'kk'){
		require_once(Route::getModelPath('pendaftar'));
		
		$a_data = mPendaftar::getData($conn,$r_id);
		$r_file = $a_data['filekk'];
		$r_idfile = $r_id;
	}
	else if ($r_jenis == 'ktpayah'){
		require_once(Route::getModelPath('pendaftar'));
		
		$a_data = mPendaftar::getData($conn,$r_id);
		$r_file = $a_data['filektpayah'];
		$r_idfile = $r_id;
	}
	else if ($r_jenis == 'ktpibu'){
		require_once(Route::getModelPath('pendaftar'));
		
		$a_data = mPendaftar::getData($conn,$r_id);
		$r_file = $a_data['filektpibu'];
		$r_idfile = $r_id;
	}
	else if ($r_jenis == 'ijazah'){
		require_once(Route::getModelPath('pendaftar'));
		
		$a_data = mPendaftar::getData($conn,$r_id);
		$r_file = $a_data['fileijazah'];
		$r_idfile = $r_id;
	}
	else
		exit();	
		
	$filename = $conf['upload_dir'].$r_jenis.'/'.($r_idfile ? $r_idfile : $r_file);
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
