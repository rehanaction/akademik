<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    $conn->debug = false;	
	// hak akses
	Modul::getFileAuth('repp_rekapaset');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
    $r_showchild = CStr::removeSpecial($_REQUEST['showchild']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Daftar Barang :.';	
	$p_tbwidth = 600;
	$p_ncol = 3;
	$p_namafile = 'rekap_barang_'.$r_unit.'_'.$r_lokasi;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	if(!empty($r_lokasi))
    	$a_lokasi = mLaporan::getDataLokasi($conn, $r_lokasi);
    
	$sql ="select s.idbarang,b.namabarang,count(s.idseri) as total 
	   from aset.as_seri s 
	   join aset.ms_barang b on b.idbarang = s.idbarang ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) ";

    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
        $sql .= "and s.idunit = '$r_unit' ";

    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    $sql .= "group by s.idbarang,b.namabarang order by s.idbarang";

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
    Rekap Daftar Barang
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
    <?  if(!empty($r_lokasi)){ ?>
	<tr valign="top">
		<td>Lokasi</td>
		<td>:</td>
		<td><?= $a_lokasi['idlokasi'] ?> - <?= $a_lokasi['namalokasi'] ?></td>
	</tr>
	<?  } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th>Barang</th>
	    <th width="80">Jumlah</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?= $i; ?>.</td>
	    <td><?= $row['idbarang'].' - '.$row['namabarang'] ?></td>
	    <td align="right"><?= CStr::formatNumber($row['total']) ?></td>
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
