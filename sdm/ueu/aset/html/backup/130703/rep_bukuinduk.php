<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_bukuinduk');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	//$r_barang = CStr::removeSpecial($_REQUEST['barang']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Buku Induk :.';	
	$p_tbwidth = 1100;
	$p_ncol = 12;
	$p_namafile = 'buku_induk_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_barang = mLaporan::getDataBarang($conn,$r_barang);
    //$rs = mLaporan::getBukuInduk($conn, $a_param);
    
	$sql ="select p.idperolehan, p.idjenisperolehan, p.idunit, u.namaunit, pd.idlokasi, l.namalokasi, p.idbarang1, b.namabarang, 
					p.idsatuan, convert(varchar(10), p.tglpembukuan, 105) as tglpembukuan, convert(varchar(10), p.tglperolehan, 105) as tglperolehan, 
					p.nobukti, p.idsumberdana, pd.qty, p.harga, pg.namadepan+' '+pg.gelarbelakang as pegawai, p.merk, p.spesifikasi
					from aset.as_perolehan p 
					join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
					left join aset.ms_lokasi l on l.idlokasi=pd.idlokasi
					left join aset.ms_barang1 b on b.idbarang1=p.idbarang1
					left join aset.ms_unit u on u.idunit=p.idunit
					left join aset.ms_jenisperolehan jp on jp.idjenisperolehan=p.idjenisperolehan
					left join sdm.ms_pegawai pg on pg.idpegawai=pd.idpegawai
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}
					order by p.idperolehan";
	
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
    Laporan Buku Induk
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
	    <th width="80">Tgl. Buku</th>
	    <th width="100">Kode Barang</th>
	    <th>Nama Barang</th>
	    <th width="100">Merk</th>
	    <th width="150">Spesifikasi</th>
	    <th width="40">Qty</th>
	    <th width="50">Satuan</th>
	    <th width="80">Harga (Rp)</th>
	    <th width="50">Sumber Dana</th>
	    <th width="100">Unit</th>
	    <th width="100">Ruang</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?=$row['tglperolehan']?></td>
	    <td align="center"><?=$row['idbarang1']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="left"><?=$row['merk']?></td>
	    <td align="left"><?=$row['spesifikasi']?></td>
	    <td align="right"><?=$row['qty']?></td>
	    <td align="center"><?=$row['idsatuan']?></td>
	    <td align="right"><?=$row['harga']?></td>
	    <td align="center"><?=$row['idsumberdana']?></td>
	    <td align="left"><?=$row['namaunit']?></td>
	    <td align="left"><?=$row['idlokasi']?></td>
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
