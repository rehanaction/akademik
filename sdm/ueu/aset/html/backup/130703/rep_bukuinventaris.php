<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_bukuinventaris');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	//$r_jnsrawat = CStr::removeSpecial($_REQUEST['jenisrawat']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Buku Inventaris :.';	
	$p_tbwidth = 1200;
	$p_ncol = 13;
	$p_namafile = 'buku_inventaris_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_jnsrawat = mLaporan::getJenisRawat($conn,$r_jnsrawat);
    //$rs = mLaporan::getBukuInventaris($conn, $a_param);
    
	$sql ="select p.nobukti, convert(varchar(12), p.tglpembukuan, 106) as tglpembukuan, b.namabarang, p.idbarang1, p.merk, 
					p.idsumberdana, pd.qty, p.harga, isnull((pd.qty*p.harga),0) as jumlah, p.catatan, u.namaunit, pd.idlokasi
					from aset.as_perolehan p
					join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
					left join aset.ms_barang1 b on b.idbarang1=p.idbarang1
					left join aset.ms_unit u on u.idunit=p.idunit
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}
					order by p.nobukti";
	
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
    Buku Inventaris
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
	    <th width="100">No. Bukti</th>
	    <th width="80">Tgl. Pembukuan</th>
	    <th>Nama Barang</th>
	    <th width="100">Kode Barang</th>
	    <th width="100">Merk / Type</th>
	    <th width="60">Sumber Dana</th>
	    <th width="50">Jumlah Barang</th>
	    <th width="75">Harga</th>
	    <th width="75">Jumlah</th>
	    <th width="150">Keterangan</th>
	    <!--th width="100">Unit</th-->
	    <th width="50">Kode Lokasi</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td align="center"><?=$row['nobukti']?></td>
	    <td align="center"><?=$row['tglpembukuan']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="center"><?=$row['idbarang1']?></td>
	    <td align="left"><?=$row['merk']?></td>
	    <td align="center"><?=$row['idsumberdana']?></td>
	    <td align="right"><?=$row['qty']?></td>
	    <td align="right"><?=$row['harga']?></td>
	    <td align="right"><?=$row['jumlah']?></td>
	    <td align="left"><?=$row['catatan']?></td>
	    <!--td align="left"><?=$row['namaunit']?></td-->
	    <td align="center"><?=$row['idlokasi']?></td>
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
