<?php
	/*
	define( '__VALID_ENTRANCE', 1 );
	
	require_once('init.php');
	
	// cek login
	$i_page = CStr::cAlphaNum($_GET['page'],'_');
	if(empty($i_page) )
	$i_page = 'home';
	
	// tampilkan halaman
	if((substr($i_page,0,1))=="c"){
		$i_pagedir = $conf['view_dir'].$i_page.'.php';
		require_once($i_pagedir);
	}else{
		require_once($conf['ui_dir'].'home.php');
		Home::display($i_page);
	}
	*/
	
	define( '__VALID_ENTRANCE', 1 );
	
        $i_page = $_GET['page'];
        
	require_once('init.php');
	
	// cek login
        $i_page = CStr::cAlphaNum($i_page,'_');
	if(empty($i_page))
		$i_page = 'home';
	
	// tampilkan halaman
	$i_pagedir = $conf['view_dir'].$i_page.'.php';
	if(is_readable($i_pagedir))
		require_once($i_pagedir);
	else
		require_once($conf['view_dir'].'error.php');
?>