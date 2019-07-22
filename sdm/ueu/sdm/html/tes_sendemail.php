<? 
	
	require_once(Route::getModelPath('email'));
	
	$p_model = mEmail;
	
	//$p_model::requestCuti($conn, 13);
	
	//$p_model::confirmCuti($conn, 13);

	echo $p_model::sendSlipGaji($conn, '201410','1570');

	//$p_model::tesMail();
	
?>