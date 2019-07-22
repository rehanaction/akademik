<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	Modul::getFileAuth('repp_perolehan');
	
	// variabel post
//	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	$r_sumber = Modul::setRequest($_POST['sumber'],'SUMBER');
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
    $p_title = '.: Laporan Rekap Perolehan Habis Pakai :.';		
	$p_tbwidth = 800;
	$p_ncol = 11;
    $p_namafile = 'rekap_perolehan_hp_'.$r_unit;
	
	//$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	$a_bulan = mCombo::bulan();
    
	$sql ="select p.tgltransaksi, p.nopo, p.tglpo, jt.jenistranshp, s.namasupplier, sum(pd.total) as total
					from aset.as_transhp p
					join aset.as_transhpdetail pd on pd.idtranshp=p.idtranshp
					left join aset.ms_supplier s on s.idsupplier = p.idsupplier
					left join aset.ms_jenistranshp jt on jt.idjenistranshp = p.idjenistranshp
					where datepart(year,p.tgltransaksi) = '$r_tahun' and datepart(month,p.tgltransaksi) between '$r_bulan1' and '$r_bulan2'
					and p.isverify = '1'
					and p.tok = 'T'
					and p.idjenistranshp != 209
					group by p.tgltransaksi, p.nopo, p.tglpo, jt.jenistranshp, s.namasupplier, p.idtranshp
					order by p.tgltransaksi";

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
    Laporan Rekap Perolehan Habis Pakai<br/>
    Universitas Esa Unggul<br/>
    Periode 
	<? if($r_bulan1 == $r_bulan2) { ?>
		<?= $a_bulan[$r_bulan1] ?>
	<? } else { ?>
		<?= $a_bulan[$r_bulan1] ?> - <?= $a_bulan[$r_bulan2] ?> 
	<? } ?>
	<?= $r_tahun ?>
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
	    <th width="90">Tgl. Perolehan</th>
	    <th width="125">Jenis Perolehan</th>
	    <th width="100">No. PO</th>
	    <th width="80">Tgl. PO</th>
	    <th>Supplier</th>
	    <th width="100">Total</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
        $nilai = (float)$row['total'];
        $grandtotal += $nilai;
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td align="center"><?= CStr::formatDateInd($row['tgltransaksi'],false) ?></td>
	    <td><?=$row['jenistranshp']?></td>
	    <td><?= $row['nopo'] ?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglpo'],false) ?></td>
	    <td><?= $row['namasupplier']?></td>
	    <td align="right"><?= CStr::formatNumber($row['total'],2) ?></td>
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
		<td colspan="6" align="right"><b>Grand Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$grandtotal,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
