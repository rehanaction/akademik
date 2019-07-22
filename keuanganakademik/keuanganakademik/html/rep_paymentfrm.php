<?
$conn->debug=false;
require_once(Route::getModelPath('pembayaranfrm'));

$notoken = $_POST['notoken'];
$data = mPembayaranfrm::getDatabytoken($conn,$notoken);

if($data['flagbatal']=='1')
	$images = "images/void.jpg";
else
	$images = "images/lunas.jpg";
?>

<html>
<head>
	<title>Kwitansi Token</title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/stylerep.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="window.print();">
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
            	<td colspan="4" align="center"><u>Kwitansi Pembelian Formulir</u></td>
            </tr>
        	<tr>
            	<td width="15%">Kasir</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999" width="35%">
                <?=$data['nip']?>
                </td>
                <td width="15%">Gelombang</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['namagelombang']?>
                </td>
            </tr>
            <tr>
            	<td width="15%">Tgl Pembayaran</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=CSTr::FormatDate($data['tglbayar'])?>
                </td>
                <td width="15%">Sistem Kuliah</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['namasistem']?>
                </td>
            </tr>
            <tr>
            	<td width="15%">No. Kuitansi</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['refno']?>
                </td>
                <td width="15%">Jalur Penerimaan</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['jalurpenerimaan']?>
                </td>
            </tr>
            <tr>
            	<td width="15%">Nominal</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=CSTr::FormatNumber($data['jumlahbayar'])?>
                </td>
                <td width="15%">Program Pendidikan</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['programpend']?>
                </td>
            </tr>
            <tr>
            	<td width="15%">Catatan</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['catatan']?>
                </td>
                <td width="15%">Jumlah Pilihan</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['jumlahpilihan']?>
                </td>
            </tr>
            <tr>
            	<td colspan="4">
                <table width="100%">
                	<tr>
                    	<td valign="top">
                        	<table>
                            	<tr valign="top">
                                	<td>
                                    Token<br>
                                    <font size="+4" color="#999999">
                                    <?=$data['notoken']?>
                                    </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="15%" align="center">
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
    <tr>
    	<td colspan="3">
        Token / Voucher hanya dapat di gunakan sekali untuk pendaftaran online 
        </td>
    </tr>
</table>
</center>
</body>
</html>
