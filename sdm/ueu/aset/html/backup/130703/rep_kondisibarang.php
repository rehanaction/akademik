<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_rekapkondisibarang');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_kondisi = CStr::removeSpecial($_REQUEST['kondisi']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Kondisi Barang :.';	
	$p_tbwidth = 400;
	$p_ncol = 3;
	$p_namafile = 'rekap_kondisi_barang_'.$r_unit.'_'.$kondisi;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idkondisi' => $r_kondisi, 'unit' => $a_unit);
	$a_kondisi = mLaporan::getDataKondisi($conn,$r_kondisi);
    //$rs = mLaporan::getRekapKondisiBarang($conn, $a_param);
    
	$sql ="select s.idbarang1, b.namabarang
					from aset.as_seri s 
					left join aset.ms_barang1 b on b.idbarang1=s.idbarang1 
					left join aset.ms_kondisi k on k.idkondisi=s.idkondisi
					left join aset.ms_unit u on u.idunit=s.idunit
					where k.idkondisi = '$r_kondisi' and
					u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} 
					order by s.idbarang1";
	
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
    Rekap Kondisi Barang
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
	<tr valign="top">
		<td width="100">Kondisi Barang</td>
		<td width="5">:</td>
		<td><?= $a_kondisi['idkondisi'] ?> - <?= $a_kondisi['kondisi'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th width="100">ID. Barang</th>
	    <th>Nama Barang</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?=$row['idbarang1']?></td>
	    <td><?=$row['namabarang']?></td>
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
