<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_pertumbuhan');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_showchild = CStr::removeSpecial($_REQUEST['showchild']);
	$r_bulan = CStr::removeSpecial($_REQUEST['bulan']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	//$ltgl = $r_tahun.'-'.$r_bulan.'-1';
	//echo $ltgl;
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Pertumbuhan Barang :.';	
	$p_tbwidth = 600;
	$p_ncol = 6;
	$p_namafile = 'pertumbuhan_barang_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);

	$sql = "select p.idbarang1, b.namabarang, sum(p.qty) as jumlah
        from aset.as_perolehan p join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where (1=1) ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
    $sql .= " group by p.idbarang1, b.namabarang order by p.idbarang1";


	
	$sql = "select p.idbarang1, b.namabarang, sum(p.qty) as jumlah
        from aset.as_perolehan p join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where (1=1) ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
    $sql .= " group by p.idbarang1, b.namabarang order by p.idbarang1";
	
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
    Pertumbuhan Barang
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
	    <th width="100">Jumlah</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td align="center"><?=$row['idbarang1']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="right"><?=$row['jumlah']?></td>
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
