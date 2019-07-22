<html>
<head>
<title>PHPMailer - Sendmail basic test</title>
</head>
<body>

<?php

require_once('../class.phpmailer.php');

$mail             = new PHPMailer(); // defaults to using php "mail()"

$mail->IsSendmail(); // telling the class to use SendMail transport

$body             = file_get_contents('contents.html');
$body             = eregi_replace("[\]",'',$body);

$mail->AddReplyTo("sim@esaunggul.ac.id","First Last");

$mail->SetFrom('sim@esaunggul.ac.id', 'First Last');

$mail->AddReplyTo("sim@esaunggul.ac.id","First Last");

$address = "wijaya.echo@yahoo.com";
$mail->AddAddress($address, "John Doe");

$mail->Subject    = "PHPMailer Test Subject via Sendmail, basic";

$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);

$mail->AddAttachment("images/phpmailer.gif");      // attachment
$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}

?>

</body>
</html>
