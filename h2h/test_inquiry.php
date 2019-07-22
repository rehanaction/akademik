<?php
	require_once('inquiry.php');
	
	$nim = $_POST['nim'];
	//dimatikan untuk menampilkan seluruh tagihan
	//$kodetagihan = $_POST['kode'];

	
	if(empty($nim)) {
		header('Location: test_client.php');
		exit;
	}
	
	
	//$jenistagihan = mTagihan::getJenisTagihanFromKode($conn,$kodetagihan);
	

	$input = array(
				'nim'=>$nim,
				//'typeInq'=>$kodetagihan,
				'trxDateTime'=>date('Y-m-d H:i:s'),
				'transmissionDateTime'=>date('Y-m-d H:i:s'),
				'companyCode'=>$conf['test_companycode'],
				'channelID'=>$conf['test_channelid'],
				'terminalID'=>$conf['test_terminalid']
			);

	//print_r($input);
    //die();
	if($conf['test_ws']) {
		$client = new nusoap_client($conf['wsdl_path'],'wsdl');
   		 if (!$client) {
       		echo 'Please check your settings here';
        	exit;
   		 }
    	$err = $client->getError();
    
    	
		//$proxy = $client->getProxy();
		// gagal disini 
		
		$ret = inquiry($input);
    	
    	
	}
	else
		$ret = inquiry($input);
	//print_r($ret);
	$inqJSON = json_encode($input);
	echo $inqJSON;

?>
<html>
<style>
	td { border: 1px solid black }
</style>
<form method="post" action="test_payment.php">
<table cellpadding="4" cellspacing="0" style="border-collapse:collapse">
	<tr>
		<td>No</td>
		<td>ID</td>
		<td>Tagihan</td>
		<td>Periode</td>
		<td>Jumlah</td>
	</tr>
	<?php
		$i = 0;
		foreach($ret['billDetails'] as $row) {
	?>
	<tr>
		<td><?php echo ++$i ?></td>
		<td><?php echo $row['billID'] ?></td>
		<td><?php echo $row['billName'] ?></td>
		<td><?php echo $row['periode'] ?></td>
		<td><?php echo $row['billAmount'] ?></td>
	</tr>
	<?php } ?>
</table>
<br />
<input type="hidden" name="nim" value="<?php echo $nim ?>">
<input type="text" name="paymentAmount">
<input type="hidden" name="jenistagihan" value="<?php echo $jenistagihan ?>">
<input type="hidden" name="billdetails" value="<?php echo base64_encode(json_encode($ret['billDetails'])) ?>">
<?php if(!empty($ret['billDetails'])) { ?>
<input type="submit" value="Payment">
<?php } ?>
</form>
<pre><?php var_dump(json_encode($ret['billDetails'])) ?></pre>
</html>