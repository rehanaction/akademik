<?php
	require_once('payment.php');
	
	// ambil inquiry
	$nim = $_POST['nim'];
	$jenistagihan = $_POST['jenistagihan'];
	
	if(empty($nim) or empty($jenistagihan)) {
		header('Location: test_client.php');
		exit;
	}
	
	$periode = mH2H::getPeriodeSekarang($conn,$jenistagihan);
	
	// ambil tagihan
	/* if($jenistagihan == H2H_JENISFORMULIR)
		$data = mTagihan::getListInquiryFormulir($conn,$nim);
	else
		$data = mTagihan::getListInquiry($conn,$nim,$jenistagihan,$periode); */
	
	$data = json_decode(base64_decode($_POST['billdetails']),true);
	
	$n = 0;
	$total = 0;
	foreach($data as $row) {
		$n++;
		$total += (float)$row['billAmount'];
	}
	
	// di-null-kan
	/* foreach($data as $i => $row) {
		$data[$i]['billName'] = null;
		$data[$i]['billAmount'] = null;
	} */
	
	$input = array(
				'nim'=>$nim,
				'billCode'=>$jenistagihan,
				'transactionID'=>Helper::randomString(20),
				'paymentAmount'=>$total,
				'companyCode'=>$conf['test_companycode'],
				'channelID'=>$conf['test_channelid'],
				'terminalID'=>$conf['test_terminalid'],
				'trxDateTime'=>date('Y-m-d H:i:s'),
				'transmissionDateTime'=>date('Y-m-d H:i:s'),
				'numBill'=>$n,
				'billDetails'=>$data // null
			);
	
	if($conf['test_ws']) {
		$client = new nusoap_client($conf['wsdl_path'],true);
		$proxy = $client->getProxy();
		
		$ret = $proxy->payment($input);
	}
	else
		$ret = payment($input);
?>
<html>
<?php echo 'Payment '.($ret['status']['isError'] ? 'Gagal' : 'Berhasil') ?>
<br /><br />
<form method="post" action="test_reversal.php">
<input type="hidden" name="refno" value="<?php echo $ret['transactionID'] ?>">
<?php if(empty($ret['status']['isError'])) { ?>
<input type="submit" value="Reversal">
<?php } ?>
</form>
<pre><?php print_r($ret) ?></pre>
</html>