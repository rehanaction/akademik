<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_permintaanhp');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	//$r_barang = CStr::removeSpecial($_REQUEST['barang']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Permintaan Habis Pakai :.';	
	$p_tbwidth = 900;
	$p_ncol = 9;
	$p_namafile = 'rekap_permintaan_hp_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_barang = mLaporan::getDataBarang($conn,$r_barang);
    //$rs = mLaporan::getPermintaanHP($conn, $a_param);
    
	$sql ="select td.idbarang, b.namabarang, td.idsatuan, td.qty, td.harga, t.idsumberdana, s.namasupplier, 
					convert(varchar(10), t.tglpembukuan, 105) as tglpembukuan
					from aset.as_transhp t
					join aset.as_transhpdetail td on td.idtranshp=t.idtranshp
					left join aset.ms_barang b on b.idbarang=td.idbarang
					left join aset.ms_unit u on u.idunit=t.idunit
					left join aset.ms_supplier s on s.idsupplier=t.idsupplier
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} and
					t.idjenistranshp != 306
					order by td.idbarang";
	
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
    Rekap Permintaan Habis Pakai
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
	    <th width="125">Kode Barang</th>
	    <th>Nama Barang</th>
	    <th width="80">Satuan</th>
	    <th width="80">Qty</th>
	    <!--th width="80">Harga</th-->
	    <th width="60">Sumber Dana</th>
	    <th width="125">Supplier</th>
	    <th width="80">Tgl. Pembukuan</th>
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
	    <td align="center"><?=$row['idsatuan']?></td>
	    <td align="right"><?=$row['qty']?></td>
	    <!--td align="right"><?=$row['harga']?></td-->
	    <td align="center"><?=$row['idsumberdana']?></td>
	    <td align="left"><?=$row['namasupplier']?></td>
	    <td align="center"><?=$row['tglpembukuan']?></td>
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
