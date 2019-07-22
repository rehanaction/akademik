<?php
	require_once('inquiry.php');
	$nim = $_POST['nim'];
	//$kodetagihan = $_POST['kode'];

	
	if(empty($nim)) {
		header('Location: test_client.php');
		exit;
	}
	
	
	$jenistagihan = mTagihan::getJenisTagihanFromKode($conn,$kodetagihan);
	

	$input = array(
				'nim'=>$nim,
				//'typeInq'=>$kodetagihan,
    			'jenisTagihan'=>$jenistagihan,
				'trxDateTime'=>date('Y-m-d H:i:s'),
				'transmissionDateTime'=>date('Y-m-d H:i:s'),
				'companyCode'=>$conf['test_companycode'],
				'channelID'=>$conf['test_channelid'],
				'terminalID'=>$conf['test_terminalid']
			);

	
	if($conf['test_ws']) {
		$client = new nusoap_client($conf['wsdl_path'],'wsdl');
   		 if (!$client) {
       		echo 'Please check your settings here';
        	exit;
   		 }
    	$err = $client->getError();
    
    	
		$proxy = $client->getProxy();
		// gagal disini 
		
		$ret = inquiry($input);
    	
    	
	}
	else
    	
		$ret = inquiry($input);

	$inqJSON = json_encode($ret);
	echo $inqJSON;

?>