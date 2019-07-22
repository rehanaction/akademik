<?php
	require_once('init.php');
	require_once('inquiry.php');
	require_once('payment.php');
	require_once('reversal.php');
	
	$server = new nusoap_server($conf['wsdl_path']);
	
	$request = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service($request);
?>