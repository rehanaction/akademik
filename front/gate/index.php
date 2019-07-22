<?php
	define( '__VALID_ENTRANCE', 1 );
	//header('Location:maintenance.php');
	require_once('init.php');
	//print_r($_POST);
	$_SESSION['txtUserID'] =$_POST['txtUserID'];
	$_SESSION['txtPassword'] = $_POST['txtPassword'];
	// cek login
	$i_page = CStr::cAlphaNum($_GET['page'],'_s');
	if(!Modul::isAuthenticated() or empty($i_page))
		$i_page = 'login';
	
	// tampilkan halaman
	$i_pagedir = $conf['view_dir'].$i_page.'.php'; 
	if(is_readable($i_pagedir)){//echo "a".die();
		require_once($i_pagedir);}
	else{//echo "b".die();
		require_once($conf['view_dir'].'login.php');} // error404.php');
?>