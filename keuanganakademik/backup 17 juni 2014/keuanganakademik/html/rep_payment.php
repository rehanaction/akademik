<?

require_once(Route::getModelPath('pembayaran'));
require_once(Route::getModelPath('pembayarandetail'));
require_once(Route::getModelPath('akademik'));

$r_id = $_POST['idpembayaran'];
$data = mPembayaran::getDatapembayaran($conn,$r_id);
$datadetail = mPembayarandetail::getDatapembayaran($conn,$r_id);

$mhs = mAkademik::getDatamhs($conn,$data['nim']);
			
if(!$mhs)
	$mhs = mAkademik::getDatapendaftar($conn,$data['nim']);

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
    	<td width="70" height="70"><img src="images/uwp.gif"></td>
        <td align="center">
        	<font size="+2">
                <strong>Universitas Wijaya Putra</strong>
            </font>
            <br>
            Jl. Raya Benowo 1-3, Surabaya - Indonesia
            <br>
            Telp : 031 7404404 / 7413061 , Fax : 031 7404405
        </td>
    </tr>
    <tr>
    	<td colspan="2">
        <table width="100%" style="background-position:right; background-repeat:no-repeat; background-image:url(<?=$images?>)">
        	<tr>
            	<td colspan="6" align="center"><u>Kwitansi Pembayaran</u></td>
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
                        <td><strong> Periode Inquiry</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$r_periode?></td>
                    </tr>
           <tr>
            	<td><strong>Kasir</strong></td>
                <td><strong>:</strong></td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['nip']?>
                </td>
                <td><strong>Tgl Pembayaran</strong></td>
                <td><strong>:</strong></td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=CStr::formatDate($data['tglbayar'])?>
                </td>
            </tr>
            <tr>
            	<td width="15%"><strong>No. Kuitansi</strong></td>
                <td><strong>:</strong></td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['refno']?>
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
                            <th>Jenis Tagihan</th>
                            <th width="15%">Periode Tagihan</th>
                            <th width="15%">Jumlah Tagihan</th>
                            <th width="15%">Jumlah Bayar</th>
                        </tr>
                        <? if($datadetail)
                            foreach($datadetail as $i => $tagihan){?>
                        <tr>
                            <td><?=$tagihan['idtagihan']?></td>
                            <td><?=$tagihan['jenistagihan'].' - '.$tagihan['namajenistagihan']?></td>
                            <td><?=$tagihan['bulantahun']?$tagihan['bulantahun']:$tagihan['periode']?></td>
                            <td align="right"><?=CStr::FormatNumber($tagihan['nominalbayar'])?></td>
                            <td align="right"><?=CStr::FormatNumber($tagihan['nominalbayar'])?>
                            </td>
                        </tr>
                            <? }?>
                    </table>
            <table width="100%"> 
            <tr>
            	<td colspan="6">
                <table width="100%">
                	<tr>
                    	<td valign="top">
                        	
                        </td>
                        <td width="25%" align="center">
                        	<table>
                            	<tr>
                                	<td>Surabaya, <?=date('d-m-Y')?></td>
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