<?php
	define( '__VALID_ENTRANCE', 1 );
	
	require_once('init.php');
	
	// include
	require_once(Route::getModelPath('user'));
	
	$token = base64_decode($_GET['t']);
	list($err,$msg) = mUser::resetPasswordForget($conn,$token);
	
	$_SESSION[SITE_ID]['OK'] = Query::isOK($err);
	$_SESSION[SITE_ID]['ALERT'] = $msg;
	
	header('Location: index.php');
?>