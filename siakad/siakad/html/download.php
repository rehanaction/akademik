<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// variabel request
	$r_jenis = CStr::removeSpecial($_REQUEST['type']);
	$r_id = CStr::removeSpecial($_REQUEST['id']);
	
	if($r_jenis == 'berita') {
		require_once(Route::getModelPath('berita'));
		
		$a_data = mBerita::getData($conn,$r_id);
		$r_file = $a_data['gambar'];
	}
	else if($r_jenis == 'materi') {
		require_once(Route::getModelPath('materi'));
		
		$a_data = mMateri::getData($conn,$r_id);
		$r_file = $a_data['filemateri'];
	}
	else if($r_jenis == 'ijasah') {
		require_once(Route::getModelPath('mahasiswa'));
		
		$a_data = mMahasiswa::getData($conn,$r_id);
		$r_file = $a_data['fileijasah'];
	}
	else if($r_jenis == 'ktp') {
		require_once(Route::getModelPath('mahasiswa'));
		
		$a_data = mMahasiswa::getData($conn,$r_id);
		$r_file = $a_data['filektp'];
	}
	else if($r_jenis == 'kk') {
		require_once(Route::getModelPath('mahasiswa'));
		
		$a_data = mMahasiswa::getData($conn,$r_id);
		$r_file = $a_data['filekk'];
	}
	else if($r_jenis == 'tugas') {
		
		require_once(Route::getModelPath('tugas'));
		
		$a_data = mTugas::getData($conn,$r_id);
		$r_file = $a_data['filetugas'];
	}
	else if($r_jenis == 'tugaskumpul') {
		require_once(Route::getModelPath('tugas'));
		
		$a_data = mTugas::getDataPengumpulan($conn,$r_id);
		$r_file = $a_data['filetugasdikumpulkan'];
	}
	else if($r_jenis == 'jurnal') {
		require_once(Route::getModelPath('kuliah'));
		
		$a_data = mKuliah::getData($conn,$r_id);
		$r_file = $a_data['filemateri'];
	}else if($r_jenis == 'guide') {
		//die($r_jenis);
		$r_file = $r_id.'.pdf';
		$r_id = $r_id.'.pdf';
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
