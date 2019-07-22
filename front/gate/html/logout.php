<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once($conf['model_dir'].'m_user.php');
	
	// update user session
	mUser::logOut($conn);
	
	// unset session
	Modul::logOut();
	
	// kembali ke halaman login
	
	header("Location: https://elearning.inaba.ac.id/login/logout.php");
	
?>