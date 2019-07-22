<?php


	require '../includes/sendgrid-php/sendgrid-php.php';

    $from = new SendGrid\Email("support.it@inaba.ac.id");
	$subject = "Belajar Kirim Email dengan API SendGrid";
	$to = new SendGrid\Email("kristiawan.adji91@gmail.com");
	$content = new SendGrid\Content("text/plain", "Hello, ini adalah email yang dikirim melalui API");
	$mail = new SendGrid\Mail($from, $subject, $to, $content);
	$apiKey = getenv('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');
	$sg = new \SendGrid($apiKey);

	// kirim email
	$response = $sg->client->mail()->send()->post($mail);

	// untuk debugging
	echo "<pre>";
	echo $response->statusCode();
	print_r($response->headers());
	echo $response->body();
	echo "</pre>";
?>