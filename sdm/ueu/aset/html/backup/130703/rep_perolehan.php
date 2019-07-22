<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_perolehan');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Perolehan Barang :.';	
	$p_tbwidth = 850;
	$p_ncol = 6;
	$p_namafile = 'rekap_perolehan_'.$r_unit.'_'.$r_tahun.'_'.$r_bulan;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
    
	$sql ="select p.idbarang1+' - '+b.namabarang as barang, p.merk, p.spesifikasi, pd.qty, p.idsatuan,
					p.tglperolehan, p.nopo, p.tglpo, jp.jenisperolehan
					from aset.as_perolehan p
					join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
					left join aset.ms_barang1 b on b.idbarang1=p.idbarang1
					left join aset.ms_unit u on u.idunit=p.idunit
					left join aset.ms_satuan s on s.idsatuan=p.idsatuan
					left join aset.ms_jenisperolehan jp on jp.idjenisperolehan = p.idjenisperolehan
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}
					and datepart(year,p.tglperolehan) = '$r_tahun' and datepart(month,p.tglperolehan) = '$r_bulan'
					and p.isverify = '1'
					order by p.idbarang1";
	
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
    Perolehan Barang
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
	    <th width="80">Tgl. Perolehan</th>
	    <th>Nama Barang</th>
	    <th width="80">Jns. Perolehan</th>
	    <th width="200">Spesifikasi</th>
	    <th width="75">Tgl. PO</th>
	    <th width="80">No. PO</th>
	    <th width="50">Jumlah</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
	    <td><?=$row['barang']?></td>
	    <td><?=$row['jenisperolehan']?></td>
	    <td align="left"><?=$row['spesifikasi']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglpo'],false) ?></td>
	    <td><?= $row['nopo'] ?></td>
	    <td align="right"><?= CStr::formatNumber($row['qty']) ?></td>
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
