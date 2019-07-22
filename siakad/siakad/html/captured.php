<?php
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
/* JPEGCam Test Script */
/* Receives JPEG webcam submission and saves to local file. */
/* Make sure your directory has permission to write files as your web server user! */

//$r_name=$_POST['filename'];
// $r_name=Helper::removeSpecial($_REQUEST['key']);
// $r_code=Helper::removeSpecial($_REQUEST['code']);

$r_name=$_REQUEST['key'];

$angkatan=$_GET['angkatan'];


	$filename = 'uploads/fotomhs/'.$angkatan.'/'; //.$r_name.'.jpg';
	if(!(is_dir($filename)))
		mkdir($filename,0777);
	$filename .= $r_name.'.jpg';


$result = file_put_contents( $filename, file_get_contents('php://input') );
if (!$result) {
	print "ERROR: Failed to write data to $filename, check permissions\n";
	exit();
}

// $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $filename;
$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $filename;
echo $filename;

?>
