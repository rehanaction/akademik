<?php
	require_once(Route::getModelPath('email'));
	
	$tujuan = 'dayat.developer@gmail.com';
			$subject='Slip Rekap Honor Mengajar';
			$body='Test Honor';
	list($err,$msg)=mEmail::sendMail($tujuan,$subject,$body);
?>
