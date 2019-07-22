<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporan'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_detail = CStr::removeSpecial($_REQUEST['iddetail']);
	$r_format = $_REQUEST['format'];
	
	if(!empty($r_key))
	
	// properti halaman
	$p_title = 'Berita Acara Serah Terima';
	$p_tbwidth = 700;
	$p_ncol = 10;
	$p_namafile = 'bastb_new_perolehanhp_'.$r_key;
	
    $sql = "select u.kodeunit, u.namaunit, t.tgltransaksi, t.nopo, t.tglpo
              from aset.as_transhp t 
              left join aset.ms_unit u on u.idunit = t.idunit 
              where t.idtranshp = '$r_key' ";

	$data =  $conn->GetRow($sql);

	$sql = "select iddettranshp,d.idbarang1,b.namabarang,d.idsatuan,qty,harga,total
			from aset.as_transhpdetail d 
			left join aset.ms_barang1 b on b.idbarang1 = d.idbarang1 
			where d.idtranshp = '$r_key' ";

	$rs = $conn->Execute($sql);

	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/stylerep.css" rel="stylesheet" type="text/css">
</head>
<body>
<div align="center">
<?php
    include('inc_headerlap.php');
?>
<div class="div_head">
    Berita Acara Serah Terima (BAST)<br> Perolehan Barang Habis Pakai / ATK<br/>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
	    <td colspan="3">
	        Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N',strtotime($data['tgltransaksi']))) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	        tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd($data['tgltransaksi']) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
	        telah diserahkan barang di lingkungan Universitas Esa Unggul dari perolehan barang habis pakai (ATK) dengan rincian sebagai berikut  :
	    </td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="80">Unit</td>
		<td width="5">:</td>
		<td><?= $data['kodeunit'] ?> - <?= $data['namaunit'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">No. PO</td>
		<td width="5">:</td>
		<td><?= $data['nopo'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">Tanggal PO</td>
		<td width="5">:</td>
		<td><?= Cstr::formatDateInd($data['tglpo']) ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No.</th>
		<th>Barang</th>
		<th width="60">Jumlah</th>
		<th width="60">Satuan</th>
		<th width="80">Harga Satuan</th>
    	<th width="100">Total</th>
	</tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
			$jumlah += (float)$row['total'];
    ?>
	<tr valign="top">
		<td align="center"><?= $i ?>.</td>
		<td><?= $row['idbarang1'].' - '.$row['namabarang'] ?></td>
		<td align="right"><?= Cstr::formatNumber($row['qty'],2) ?></td>
		<td><?= $row['idsatuan'] ?></td>
		<td align="right"><?= Cstr::formatNumber($row['harga'],2) ?></td>
		<td align="right"><?= Cstr::formatNumber($row['total'],2) ?></td>
	</tr>
    <?php
        }
        if($i == 0){
    ?>
	<tr>
		<td colspan="10" align="center">-- Data tidak ditemukan --</td>
    </tr>
    <?php
        }
    ?>
	<tr>
		<td colspan="5" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jumlah,2) ?></b></td>
    </tr>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">Demikian berita acara ini dibuat dengan sebenar - benarnya dan digunakan sebagaimana mestinya.</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td width="35%">Jakarta, &nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?><?//= str_repeat('.',40) ?></td>
		<td width="35%">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Biro Pembelian</td>
		<td>Pengelola Aset</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>Ka. Biro Pembelian</td>
		<td>Ka. Bag. RT</td>
	</tr>
</table>
</div>
</body>
</html>
