<?php
	define( '__VALID_ENTRANCE', 1 );
		
	require_once('init.php');
	
	// cek login
	$i_page = CStr::cAlphaNum($_GET['page'],'_');
	if(empty($i_page))
		$i_page = 'home';
	
	// tampilkan halaman
	$i_pagedir = $conf['view_dir'].$i_page.'.php';
	if(is_readable($i_pagedir))
		require_once($i_pagedir);
	else
		require_once($conf['view_dir'].'home.php'); // error404.php');
?>
