<?php
	define( '__VALID_ENTRANCE', 1 );
		
	require_once('init.php');
		
	// cek login
	$i_page = CStr::cAlphaNum($_GET['page'],'_');
	if(empty($i_page))
		$i_page = 'home';
	
	// tampilkan doctype
	if(substr($i_page,0,4) == 'ajax' or substr($i_page,0,4) == 'xinc')
		$conn->debug = false;
	else
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'."\n";
	
	// tampilkan halaman
	$i_pagedir = $conf['view_dir'].$i_page.'.php';
	if(is_readable($i_pagedir))
		require_once($i_pagedir);
	else
		require_once($conf['view_dir'].'error404.php'); // home.php');
	
	// untuk pdf :D
	if($r_format == 'pdf') {
		// Page::savePDF($p_namafile);
		Page::saveWkPDF($p_namafile);
	}

?>
