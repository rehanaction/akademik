<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_unit');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_barang = CStr::removeSpecial($_REQUEST['barang']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Daftar Unit :.';	
	$p_tbwidth = 650;
	$p_ncol = 5;
	$p_namafile = 'daftar_kode_unit';
	
	$sql =" select a.idunit, a.kodeunit, a.namaunit, a.namasingkat, b.kodeunit as unit, a.level
			from aset.ms_unit a 
			left join aset.ms_unit b on a.parentunit = b.idunit 
			order by a.infoleft ";
	
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
    Laporan Daftar Unit<br/>
    Universitas Esa Unggul<br/>
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
	    <th width="60">Kode Unit</th>
	    <th>Nama Unit</th>
		<th width="60">Nama Singkat</th>
	    <th width="75">Parent Unit</th>
	    <th width="40">Level</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td><?=$row['kodeunit']?></td>
	    <td><?=($row['level'] > 1)?str_repeat('&nbsp;',$row['level']*4).$row['namaunit']:$row['namaunit']?></td>
	    <td><?=$row['namasingkat']?></td>
		<td><?=$row['unit']?></td>
	    <td align="center"><?=$row['level']?></td>
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
