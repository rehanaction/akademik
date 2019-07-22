<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_keupenyusutan');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	//$r_jnsrawat = CStr::removeSpecial($_REQUEST['jenisrawat']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Penyusutan Aset :.';	
	$p_tbwidth = 900;
	$p_ncol = 6;
	$p_namafile = 'penyusutan_aset_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_jnsrawat = mLaporan::getJenisRawat($conn,$r_jnsrawat);
    //$rs = mLaporan::getPenyusutanAset($conn, $a_param);
    
	$sql ="select d.periode, s.idbarang, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, s.idunit, 
					jp.jenispenyusutan, d.nilaisusut, d.nilaiaset, u.kodeunit 
					from aset.as_histdepresiasi d 
					left join aset.as_seri s on s.idseri=d.idseri 
					left join aset.ms_barang b on b.idbarang=s.idbarang 
					left join aset.ms_jenispenyusutan jp on jp.idjenispenyusutan=d.idjenispenyusutan 
					left join aset.ms_unit u on u.idunit=s.idunit 
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} 
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
    Penyusutan Aset
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
	    <th width="100">Kode. Barang</th>
	    <th>Nama Barang</th>
	    <th width="150">Nilai Aset</th>
	    <th width="200">Jenis Penyusutan</th>
	    <th width="150">Nilai Penyusutan</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td align="center"><?=$row['idbarang']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="right"><?=$row['nilaiaset']?></td>
	    <td align="left"><?=$row['jenispenyusutan']?></td>
	    <td align="right"><?=$row['nilaisusut']?></td>
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
