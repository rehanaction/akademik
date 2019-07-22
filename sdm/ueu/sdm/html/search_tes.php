<? 
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	$conn->debug = false;
	
	if(isset($_GET['q'])){
		$param = strtolower(($_GET['q'])); 
		
		
		echo 'asdfasdfasdf';//json_encode($data);
	}
?>