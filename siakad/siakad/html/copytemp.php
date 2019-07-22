<?php 
	//defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	$getfile=$_POST['getfile'];
	$filename=$_POST['filename'];
	
	if(copy($filename, $getfile)){
		echo 'success';
	}else{
		echo 'fail';
	}
?>	
