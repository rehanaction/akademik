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
<body>
<center>
<table width="100%" cellpadding="4" cellspacing="0" style="border:thin" border="1">
	<tr>
				<td colspan="3">
				<? require_once('inc_headrep.php');?>
				</td>
			</tr>
    <tr>
    	<td colspan="2">
        <table width="100%" style="background-position:right; background-repeat:no-repeat; background-image:url(<?=$images?>)">
        	<tr>
            	<td colspan="6" align="center"><b>Bukti Pembayaran</b></td>
            </tr>
            <tr>
                <td colspan="6" align="center"><b>&nbsp;</b></td>
            </tr>
            <tr>
                    	<td  width="13%"><strong> NIM / Nopendaftar</strong></td>
                        <td width="1%"><strong>:</strong></td>
                        <td width="35%" style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
							<?=$data['nim']?$data['nim']:$data['nopendaftar']?>
                        </td>
                        <td width="15%"><strong> Sistem Kuliah</strong></td>
                        <td width="1%"><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['namasistem']?></td>
                    </tr>
					<tr>
                    	<td><strong> Nama</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['nama']?></td>
                        <td><strong> Jalur Penerimaan</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['namajalur']?></td>
                    </tr>
					<tr>
                    	<td><strong> Jurusan</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['kodeunit'].' '.$mhs['namaunit']?></td>
						<?php if($r_deposit) { ?>
						<td><strong> Periode Deposit</strong></td>
						<td><strong>:</strong></td>
						<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$data['periode']?></td>
						<?php } else { ?>
                        <td><strong> Periode Bayar</strong></td>
						<td><strong>:</strong></td>
						<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$data['periodebayar']?></td>
						<?php } ?>
                    </tr>
           <tr>
            	<td><strong>Petugas</strong></td>
                <td><strong>:</strong></td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['nama']?>
                </td>
				<td><strong>Tgl Pembayaran</strong></td>
                <td><strong>:</strong></td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?= CStr::formatDateInd($data['tglbayar']) ?>
                </td>
            </tr>
			<?php if(!$r_deposit) { ?>
            <tr>
            	<td width="15%"><strong>No. Pembayaran</strong></td>
                <td><strong>:</strong></td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['refno']?>
                </td>
				<td><strong> Bank</strong></td>
				<td><strong>:</strong></td>
				<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
				<?=$data['bankname']?>
				</td>
            </tr>
			<?php } ?>
            <tr>
            	<td colspan="6">
                </td>
            </tr>
            </table>
            <br>
            <table width="100%">
                     	<tr>
                            <td colspan="4" bgcolor="#CCCCCC"><strong>Detail Pembayaran</strong></td>
                        </tr>
                        <tr bgcolor="#CCCCCC">
                            <th width="15%">Kode Tagihan</th>
                            <th>Jenis Tagihan</th>
                            <th width="15%">Jumlah Tagihan</th>
                            <th width="15%">Jumlah Bayar</th>
                        </tr>
                        <? if($datadetail)
                            $total=0;
                            foreach($datadetail as $i => $tagihan){?>

                        <tr>
                            <td><?=$tagihan['idtagihan']?></td>
                            <td><?=$tagihan['namajenistagihan']?></td>
                            <td align="right">Rp. <?=CStr::FormatNumber($tagihan['nominalbayar'])?></td>
                            <td align="right">Rp. <?=CStr::FormatNumber($tagihan['nominalbayar'])?>
                            </td>
                        </tr>
                            
                            <?php $total += $tagihan['nominalbayar']; } ?>
                        <tr>
                            <td colspan="4"><hr></td>
                        </tr>
                        <tr>
                            <td align="right" colspan="3"><b>Total</b></td>
                            <td align="right"><b>Rp. <?= CStr::FormatNumber($total); ?></b></td>
                        </tr>
                        <tr>
                            <td align="right" colspan="3"><b>Jumlah Uang</b></td>
                            <td align="right"><b>Rp. <?=CStr::FormatNumber($data['jumlahuang'])?></b></td>
                        </tr>
                        <tr>
                            <td align="right" colspan="3"><b>Cash Back</b></td>
                            <td align="right">Rp. <?=CStr::FormatNumber($data['jumlahuang']-$data['jumlahbayar'])?></td>
                        </tr>
                        <tr>
                            <td colspan="4"><hr></td>
                        </tr>
                    </table>
            <table width="100%"> 
            <tr>
            	<td colspan="4">
                <table width="100%">
                	<tr>
                    	<td valign="top">
                        	
                        </td>
                        <td width="50%" align="right">
                        	<table>
                            	<tr>
                                	<td>Bandung, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
                                </tr>
                                
                                <tr>
                                	<td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><center><?=$data['nama']?></center></td>
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
