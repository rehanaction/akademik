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
	$p_tbwidth = 550;
	$p_ncol = 5;
	$p_namafile = 'stock_habis_pakai_'.$r_unit.'_'.$barang;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idbarang' => $r_barang, 'unit' => $a_unit);
	$a_barang = mLaporan::getDataBarang($conn,$r_barang);
    //$rs = mLaporan::getStockHabisPakai($conn, $a_param);
    
	$sql ="select s.idbarang, b.namabarang, s.jmlstock, st.satuan
					from aset.as_stockhp s 
					left join aset.ms_barang b on b.idbarang=s.idbarang 
					left join aset.ms_satuan st on st.idsatuan=s.idsatuan
					left join aset.ms_unit u on u.idunit=s.idunit
					where s.idbarang = '$r_barang' and
					u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} and
					substring(s.idbarang,0,1)= '1'
					order by s.idbarang";
	
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
    Universitas Esa Unggul<br/>
    Stock Habis Pakai
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="60">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?></td>
	</tr>
	<tr valign="top">
		<td width="100">Barang</td>
		<td width="5">:</td>
		<td><?= $a_barang['idbarang'] ?> - <?= $a_barang['namabarang'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th width="100">ID. Barang</th>
	    <th>Nama Barang</th>
	    <th width="75">Jumlah Stock</th>
	    <th width="75">Satuan</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?=$row['idbarang']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="right"><?=$row['jmlstock']?></td>
	    <td align="left"><?=$row['satuan']?></td>
	</tr>
	<?
	}
    if($i == 0) {
	?>
	<tr>
	    <td colspan="<?= $p_ncol ?>" align="center">-- Data tidak ditemukan --</td>
	</tr>
	<? } ?>
</table>
</div>
</body>
</html>
