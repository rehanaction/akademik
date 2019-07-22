<?php
	require_once('reversal.php');
	
	// ambil payment
	$refno = $_POST['refno'];
	
	if(empty($refno)) {
		header('Location: test_client.php');
		exit;
	}
	
	$data = mTagihan::getPembayaranAllFromRefNo($conn,$refno);
	
	if(!empty($data['idpembayaranfrm']))
		$jenistagihan = H2H_JENISFORMULIR;
	else
		$jenistagihan = mTagihan::getJenisTagihanFromIDPembayaran($conn,$data['idpembayaran']);
	
	$input = array(
				'billCode'=>$jenistagihan,
				'nim'=>$data['nim'],
				'notoken'=>$data['notoken'],
				'transactionID'=>$refno,
				'paymentAmount'=>$data['jumlahbayar'],
				'companyCode'=>$conf['test_companycode'],
				'channelID'=>$conf['test_channelid'],
				'terminalID'=>$conf['test_terminalid'],
				'trxDateTime'=>date('Y-m-d H:i:s'),
				'transmissionDateTime'=>date('Y-m-d H:i:s'),
				'currency'=>H2H_CURRENCY,
				'origTrxDateTime'=>$data['trxdatetime'],
				'origTransmissionDateTime'=>$data['transmissiondatetime']
			);
	
	if($conf['test_ws']) {
		$client = new nusoap_client($conf['wsdl_path'],true);
		$proxy = $client->getProxy();
		
		$ret = reversal($input);
	}
	else
		$ret = reversal($input);
	$inqJSON = json_encode($ret);
	echo $inqJSON;
?>
