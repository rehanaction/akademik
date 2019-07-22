<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// variabel request
	$r_jenis = CStr::removeSpecial($_REQUEST['type']);
	$r_alt = CStr::removeSpecial($_REQUEST['alt']);
	$r_id = CStr::removeSpecial($_REQUEST['id']);
	
	if($r_jenis == 'berita') {
		require_once(Route::getModelPath('berita'));
		
		$a_data = mBerita::getData($conn,$r_id);
		$r_file = $a_data['gambar'];
		$r_default = 'default.jpg';
		
		if($r_alt == '2') {
			$mw = 650;
			$mh = 145;
		}
		else {
			$mw = 450;
			$mh = 100;
		}
	}
	else
		exit();
	
	$filename = $conf['upload_dir'].$r_jenis.'/'.$r_id;
	
	$img = true;
	$a_size = getimagesize($filename);
	if($a_size != -1) {
		switch($a_size[2]) {
			case IMAGETYPE_GIF: $src = imagecreatefromgif($filename); break;
			case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($filename); break;
			case IMAGETYPE_PNG: $src = imagecreatefrompng($filename); break;
			default: $img = false;
		}
	}
	else
		$img = false;
	
	if(!$img) {
		$filename = 'images/'.$r_default;
		
		$img = true;
		$a_size = getimagesize($filename);
		
		if($a_size != -1) {
			switch($a_size[2]) {
				case IMAGETYPE_GIF: $src = imagecreatefromgif($filename); break;
				case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($filename); break;
				case IMAGETYPE_PNG: $src = imagecreatefrompng($filename); break;
				default: $img = false;
			}
		}
		else
			$img = false;
	}
	
	if($img) {
		$rw = $a_size[0];
		$rh = $a_size[1];
		
		$nw = $rw;
		$nh = $rh;
		
		// resize
		if($nw > $mw or $nh > $mh) {
			if($mw > $mh) {
				$nw = $mw;
				$nh = round(($nw*$rh)/$rw);
			}
			else {
				$nh = $mh;
				$nw = round(($nh*$rw)/$rh);
			}
		}
		
		// crop
		if($nw > $mw) {
			$nw = $mw;
			$cw = round(($rh/$nh)*$nw);
		}
		else
			$cw = $rw;
		
		if($nh > $mh) {
			$nh = $mh;
			$ch = round(($rw/$nw)*$nh);
		}
		else
			$ch = $rh;
		
		$target = imagecreatetruecolor($nw,$nh);
		
		ob_clean();
		header("Content-Type: ".$a_size['mime']);
			
		imagecopyresized($target, $src, 0, 0, 0, 0, $nw, $nh, $cw, $ch);
		
		switch($a_size[2]) {
			case IMAGETYPE_GIF: imagegif($target); break;
			case IMAGETYPE_JPEG: imagejpeg($target); break;
			case IMAGETYPE_PNG: imagepng($target); break;
		}
		
		imagedestroy($src);
		imagedestroy($target);
	}
?>