<?php
require_once(Route::getModelPath('pembayaran'));
require_once(Route::getModelPath('pembayarandetail'));
require_once(Route::getModelPath('akademik'));
require_once(Route::getModelPath('deposit'));

$r_id = $_POST['idpembayaran'];
if($r_id[0] == 'D') {
	$r_deposit = true;
	$r_id = substr($r_id,1);
	
	$data = mDeposit::getData($conn,$r_id);
	
	$data['nip'] = Modul::getUserName();
	$data['tglbayar'] = date('Y-m-d');
	
	$rowt = array();
	$rowt['idtagihan'] = 'DEP'.str_pad($data['iddeposit'],21,'0',STR_PAD_LEFT);
	$rowt['jenistagihan'] = 'DEP';
	$rowt['namajenistagihan'] = 'DEPOSIT';
	$rowt['periode'] = $data['periode'];
	$rowt['nominalbayar'] = $data['nominaldeposit']-$data['nominalpakai'];
	
	$datadetail = array($rowt);
}
else {
	$r_deposit = false;
	
	$data = mPembayaran::getDatapembayaran($conn,$r_id);
	$datadetail = mPembayarandetail::getDataPembayaranDeposit($conn,$r_id);
}

$mhs = mAkademik::getDatamhs($conn,$data['nim']);
			
if(!$mhs)
	$mhs = mAkademik::getDatapendaftar($conn,$data['nopendaftar']);

if($data['flagbatal']=='1')
	$images = "images/void.jpg";
else
	$images = "images/lunas.jpg";
?>

<html>
<head>
	<title>Kwitansi Pembayaran</title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/stylerep.css" rel="stylesheet" type="text/css">
</head>
<body onload="window.print()">
<center>
<table width="100%" cellpadding="4" cellspacing="0" border="0">
    <tr>
        <td>&nbsp;<br>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right"><?=$data['nim']?$data['nim']:$data['nopendaftar']?> <?=$data['refno']?><br> <?= CStr::formatDateInd($data['tglbayar']) ?> <?=$data['nama']?></td>
    </tr>
</table>
</center>
</body>
</html>
