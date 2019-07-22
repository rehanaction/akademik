<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_stockhp');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_barang = CStr::removeSpecial($_REQUEST['barang']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Stock Habis Pakai :.';	
	$p_tbwidth = 750;
	$p_ncol = 6;
	$p_namafile = 'stock_habis_pakai_'.$r_unit.'_'.$barang;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idbarang1' => $r_barang, 'unit' => $a_unit);
	$a_barang = mLaporan::getDataBarang($conn,$r_barang);
    //$rs = mLaporan::getStockHabisPakai($conn, $a_param);
    
	$sql ="select t.idbarang1, b.namabarang, t.jmlstock, t.nilaistock, b.idsatuan
			 from aset.as_stockhp t 
			 join aset.ms_barang1 b on b.idbarang1 = t.idbarang1 
			 --where t.jmlstock > 0 
			 order by b.namabarang";
	
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
    Laporan Stock Habis Pakai<br/>
    Universitas Esa Unggul<br/>
	Periode <?= CStr::formatDateInd(date('Y-m-d')) ?>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
		<th width="125">Kode Barang</th>
	    <th>Barang</th>
	    <th width="75">Jumlah Stock</th>
	    <th width="100">Nilai Stock</th>
	    <th width="75">Satuan</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
		$jumlah += (float)$row['jmlstock'];
		$nilai += (float)$row['nilaistock'];
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
		<td><?=$row['idbarang1']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="right"><?= CStr::formatNumber($row['jmlstock']) ?></td>
	    <td align="right"><?= CStr::formatNumber($row['nilaistock'],2) ?></td>
	    <td align="left"><?=$row['idsatuan']?></td>
	</tr>
	<?
	}
    if($i == 0) {
	?>
	<tr>
	    <td colspan="<?= $p_ncol ?>" align="center">-- Data tidak ditemukan --</td>
	</tr>
	<? } ?>
	<tr>
		<td colspan="3" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jumlah) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilai,2) ?></b></td>
		<td></td>
	</tr>
</table>
</div>
</body>
</html>
