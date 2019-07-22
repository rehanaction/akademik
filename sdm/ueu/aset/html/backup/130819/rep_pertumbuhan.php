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
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Pertumbuhan Barang :.';	
	$p_tbwidth = 900;
	$p_ncol = 9;
	$p_namafile = 'pertumbuhan_barang_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
    $a_bulan = mCombo::bulan();
    $a_barang = array();
    
    //barang saja
	$sql = "select p.idbarang1, b.namabarang
        from aset.as_perolehan p join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) <= '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
    $sql .= " group by p.idbarang1, b.namabarang order by p.idbarang1";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
	    $a_barang[$row['idbarang1']]['jmla'] = 0;
	    $a_barang[$row['idbarang1']]['nilaia'] = 0;
	    $a_barang[$row['idbarang1']]['jmln'] = 0;
	    $a_barang[$row['idbarang1']]['nilain'] = 0;
	}


	//awal
	$sql = "select p.idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
        from aset.as_perolehan p join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) < '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
    $sql .= " group by p.idbarang1";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmla'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilaia'] = (int)$row['total'];
	}


	//bulan ini
	$sql = "select p.idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
        from aset.as_perolehan p join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) = '$r_tahun' and month(p.tglperolehan) = '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
    $sql .= " group by p.idbarang1";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmln'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilain'] = (int)$row['total'];
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
    Laporan Aktivitas Aset<br/>
    Universitas Esa Unggul<br/>
    Periode <?= $a_bulan[$r_bulan] ?> <?= $r_tahun ?>
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
        <th rowspan="2" width="30">No.</th>
	    <th rowspan="2" width="100">Kode. Barang</th>
	    <th rowspan="2">Nama Barang</th>
	    <th colspan="2" width="100">Saldo Awal</th>
	    <th colspan="2" width="100">Aktivitas</th>
	    <th colspan="2" width="100">Saldo Akhir</th>
    </tr>
	<tr>
		<th width="50">Jumlah</th>
		<th width="95">Nilai</th>
		<th width="50">Jumlah</th>
		<th width="95">Nilai</th>
		<th width="50">Jumlah</th>
		<th width="95">Nilai</th>
	</tr>
	<? 
	$i=0;
	foreach($a_barang as $idbarang1 => $val){
	    $i++;
	    $jml = $val['jmla']+$val['jmln'];
	    $nilai = $val['nilaia']+$val['nilain'];
		$jmla += (float)$val['jmla'];
		$jmln += (float)$val['jmln'];
		$nilaia += (float)$val['nilaia'];
		$nilain += (float)$val['nilain'];
		$jmlsaldo += $jml;
		$nilaisaldo += $nilai;	    
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td align="center"><?= $idbarang1 ?></td>
	    <td><?= $val['namabarang'] ?></td>
	    <td align="right"><?= CStr::formatNumber($val['jmla']) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['nilaia'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['jmln']) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['nilain'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($jml) ?></td>
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
		<td colspan="3" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmla) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilaia,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmln) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilain,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmlsaldo) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilaisaldo,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
