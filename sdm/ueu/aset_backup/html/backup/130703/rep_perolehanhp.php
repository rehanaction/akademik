<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_perolehanhp');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	//$r_barang = CStr::removeSpecial($_REQUEST['barang']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Perolehan Habis Pakai :.';	
	$p_tbwidth = 850;
	$p_ncol = 6;
	$p_namafile = 'rekap_perolehan_hp_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_barang = mLaporan::getDataBarang($conn,$r_barang);
    //$rs = mLaporan::getPerolehanHP($conn, $a_param);
    
	$sql ="select p.idbarang, b.namabarang, p.merk, p.spesifikasi, pd.qty, p.idsatuan
					from aset.as_perolehan p
					join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
					left join aset.ms_barang b on b.idbarang=p.idbarang
					left join aset.ms_unit u on u.idunit=p.idunit
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} and
					substring(p.idbarang,0,1) = '1'
					order by p.idbarang";
	
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
    Rekap Perolehan Habis Pakai
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
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <!--th width="90">ID. Penghapusan</th-->
	    <th width="100">Kode Barang</th>
	    <th>Nama Barang</th>
	    <th width="100">Merk</th>
	    <th width="150">Spesifikasi</th>
	    <th width="100">Jumlah - Satuan</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <!--td align="center"><?=$row['idpenghapusan']?></td-->
	    <td align="center"><?=$row['idbarang']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="left"><?=$row['merk']?></td>
	    <td align="left"><?=$row['spesifikasi']?></td>
	    <td align="center"><?=$row['qty']?> - <?= $row['idsatuan']?></td>
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
