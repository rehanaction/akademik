<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_keuaset');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	//$r_barang = CStr::removeSpecial($_REQUEST['barang']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Daftar Aset Akuntansi :.';	
	$p_tbwidth = 900;
	$p_ncol = 7;
	$p_namafile = 'daftar_aset_akuntansi_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_barang = mLaporan::getDataBarang($conn,$r_barang);
    //$rs = mLaporan::getAsetAkuntansi($conn, $a_param);
    
	$sql ="select p.idbarang, b.namabarang, p.idsatuan, pd.qty, p.harga, u.namaunit, convert(varchar(10), p.tglperolehan, 105) as tglperolehan, p.catatan,
					substring(convert(varchar(10), p.tglpembukuan, 101),4,2)+'/'+substring(convert(varchar(10), p.tglpembukuan, 101),9,2) as bln
					from aset.as_perolehan p
					join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
					left join aset.ms_barang b on b.idbarang=p.idbarang
					left join aset.ms_unit u on u.idunit=p.idunit
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}
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
    Daftar Aset Akuntansi
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
	    <th>Nama Barang</th>
	    <th width="80">Jumlah</th>
	    <th width="100">Satuan</th>
	    <th width="60">Bulan</th>
	    <th width="100">Harga Perolehan</th>
	    <th width="125">Catatan</th>
	    <th width="100">Tgl. Perolehan</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td><?=$row['namabarang']?></td>
	    <td align="right"><?=$row['qty']?></td>
	    <td align="center"><?=$row['idsatuan']?></td>
	    <td align="center"><?=$row['bln']?></td>
	    <td align="right"><?=$row['harga']?></td>
	    <td align="left"><?=$row['catatan']?></td>
	    <td align="center"><?=$row['tglperolehan']?></td>
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
