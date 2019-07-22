<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_perolehanpertahun');

	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Perolehan Aset Tahunan :.';	
	$p_tbwidth = 1200;
	$p_ncol = 15;
	$p_namafile = 'rekap_perolehan_tahunan_'.$r_tahun;
	
	$a_unit = array();
	
	//unit saja
	$sql = " select p.idunit, u.namaunit
				from aset.as_perolehan p
				join aset.ms_unit u on u.idunit = p.idunit
				where datepart(year,p.tglperolehan) = '$r_tahun' and p.isverify = 1
				group by p.idunit, u.namaunit
				order by p.idunit ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_unit[$row['idunit']]['namaunit'] = $row['namaunit'];
	}

	//januari
		$sql = " select p.idunit, u.namaunit, sum(p.total) as tot, datepart(month,p.tglperolehan) as bulan
				from aset.as_perolehan p
				join aset.ms_unit u on u.idunit = p.idunit
				where datepart(year,p.tglperolehan) = '$r_tahun' and p.isverify = 1
				group by p.idunit, u.namaunit, datepart(month,p.tglperolehan)
				order by p.idunit ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_unit[$row['idunit']][$row['bulan']] = $row['tot'];
	}
	
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
    Laporan Rekap Perolehan Aset Tahunan<br/>
    Universitas Esa Unggul<br/>
	Periode <?= $r_tahun ?>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th rowspan="2" width="30">No.</th>
	    <th rowspan="2" width="175">Unit</th>
	    <th colspan="12" width="80">Bulan</th>
   	    <th rowspan="2" width="80">Total</th>
    </tr>
	<tr>
		<th width="60">Januari</td>
		<th width="60">Februari</td>
		<th width="60">Maret</td>
		<th width="60">April</td>
		<th width="60">Mei</td>
		<th width="60">Juni</td>
		<th width="60">Juli</td>
		<th width="60">Agustus</td>
		<th width="60">September</td>
		<th width="60">Oktober</td>
		<th width="60">November</td>
		<th width="60">Desember</td>
	</tr>
	<? 
	$i=0;
	foreach($a_unit as $idunit => $val){
	    $i++;
        $nilai = (float)$val['1']+(float)$val['2']+(float)$val['3']+(float)$val['4']+(float)$val['5']+
				 (float)$val['6']+(float)$val['7']+(float)$val['8']+(float)$val['9']+(float)$val['10']+
				 (float)$val['11']+(float)$val['12'];
        $total += $nilai;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td><?= $val['namaunit'] ?></td>
	    <td align="right"><?= CStr::formatNumber($val['1'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['2'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['3'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['4'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['5'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['6'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['7'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['8'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['9'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['10'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['11'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['12'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($nilai,2) ?></td>
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
		<td colspan="14" align="right"><b>Grand Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$total,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
