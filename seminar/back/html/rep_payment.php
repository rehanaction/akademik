<?php
require_once(Route::getModelPath('pembayaran'));
require_once(Route::getModelPath('pembayarandetail'));
require_once(Route::getModelPath('akademik'));
require_once(Route::getModelPath('pesertaseminar'));
//require_once(Route::getModelPath('deposit'));

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
	$datadetail = mPembayaran::getDatapembayaran($conn,$r_id);
}

$mhs = mPesertaSeminar::getDataPeserta($conn,$data['nopeserta']);
			

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
<body>
<center>
<table width="60%" cellpadding="4" cellspacing="0" style="border:thin" border="1">
	<tr>
				<td colspan="3">
				<? require_once('inc_headrep.php');?>
				</td>
			</tr>
    <tr>
    	<td colspan="2">
        <table width="100%" style="background-position:right; background-repeat:no-repeat; background-image:url(<?=$images?>)">
        	<tr>
            	<td colspan="6" align="center"><u>Kwitansi Pembayaran</u></td>
            </tr>
            <tr>
				<td  width="13%"><strong> Nopendaftar</strong></td>
				<td width="1%"><strong>:</strong></td>
				<td width="35%" style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
					<?=$mhs['nopendaftar']?>
				</td>
            	<td><strong>Kasir</strong></td>
                <td><strong>:</strong></td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['nip']?>
                </td>
			</tr>
			<tr>
				<td><strong> Nama</strong></td>
				<td><strong>:</strong></td>
				<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['nama']?></td>
				<td><strong>Tgl Pembayaran</strong></td>
                <td><strong>:</strong></td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=CStr::formatDate($data['tglbayar'])?>
                </td>
			</tr>
			<tr>
				<td><strong> Seminar</strong></td>
				<td><strong>:</strong></td>
				<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['namaseminar']?></td>                 
            	<td width="15%"><strong>No. Kuitansi</strong></td>
                <td><strong>:</strong></td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['nokuitansi']?>
                </td>
			</tr>

            <tr>
            	<td colspan="6">
                </td>
            </tr>
            </table>
            <table width="100%">
                     	<tr>
                            <td colspan="5" bgcolor="#CCCCCC"><strong>Detail Pembayaran</strong></td>
                        </tr>
                        <tr bgcolor="#CCCCCC">
                            <th width="15%">Id Tagihan</th>
                            <th width="15%">Periode Tagihan</th>
                            <th width="15%">Jumlah Tagihan</th>
                            <th width="15%">Jumlah Bayar</th>
                        </tr>
                        <? if($datadetail)
                           // foreach($datadetail as $i => $tagihan){
                           $tagihan = $datadetail;
                           ?>
                        <tr>
                            <td><?=$tagihan['idtagihan']?></td>
                            <td><?=$tagihan['bulantahun']?$tagihan['bulantahun']:$tagihan['periode']?></td>
                            <td align="right"><?=CStr::FormatNumber($tagihan['jumlahbayar'])?></td>
                            <td align="right"><?=CStr::FormatNumber($tagihan['jumlahuang'])?>
                            </td>
                        </tr>
                            <? //}?>
                    </table>
            <table width="100%"> 
            <tr>
            	<td colspan="5">
                <table width="100%">
                	<tr>
                    	<td valign="top">
                        	
                        </td>
                        <td width="25%" align="center">
                        	<table>
                            	<tr>
                                	<td>Jakarta, <?=date('d-m-Y')?></td>
                                </tr>
                                <tr>
                                	<td><?=$data['nip']?></td>
                                </tr>
                                <tr>
                                	<td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td style="border-bottom:dotted; border-bottom-width:thin; border-bottom-color:#999"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                </td> 
            </tr>
        </table>
        </td>
    </tr>
</table>
</center>
</body>
</html>
