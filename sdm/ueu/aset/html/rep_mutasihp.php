<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_mutasihp');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_bulan = CStr::removeSpecial($_REQUEST['bulan']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Aktivitas Habis Pakai :.';	
	$p_tbwidth = 1000;
	$p_ncol = 12;
	$p_namafile = 'mutasi_hp_'.$r_unit;
	
	//$a_unit = mLaporan::getDataUnit($conn, $r_unit);
    $a_bulan = mCombo::bulan();
	$a_barang = array();

    //barang saja
	$sql = "select td.idbarang1, b.namabarang, b.idsatuan
        from aset.as_transhp t 
		join aset.as_transhpdetail td on td.idtranshp = t.idtranshp
		join aset.ms_barang1 b on b.idbarang1 = td.idbarang1
		where year(t.tgltransaksi) <= '$r_tahun' and month(t.tgltransaksi) <= '$r_bulan' ";

    $sql .= " group by td.idbarang1, b.namabarang, b.idsatuan order by b.namabarang";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
	    $a_barang[$row['idbarang1']]['idsatuan'] = $row['idsatuan'];
	    $a_barang[$row['idbarang1']]['jmla'] = 0;
	    $a_barang[$row['idbarang1']]['nilaia'] = 0;
	    $a_barang[$row['idbarang1']]['jmlt'] = 0;
	    $a_barang[$row['idbarang1']]['jmlk'] = 0;
	    $a_barang[$row['idbarang1']]['nilait'] = 0;
	    $a_barang[$row['idbarang1']]['nilaik'] = 0;
	}

	//awal
	$sql = "select td.idbarang1, t.tok, sum(td.qty) as jumlah, sum(td.total) as total
        from aset.as_transhp t 
		join aset.as_transhpdetail td on td.idtranshp = t.idtranshp
		join aset.ms_barang1 b on b.idbarang1 = td.idbarang1
		where year(t.tgltransaksi) <= '$r_tahun' and month(t.tgltransaksi) < '$r_bulan' ";

    $sql .= " group by td.idbarang1, t.tok order by td.idbarang1";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
		if($row['tok'] == 'T'){
			$a_barang[$row['idbarang1']]['jmla'] += (float)$row['jumlah'];
			$a_barang[$row['idbarang1']]['nilaia'] += (float)$row['total'];
		}else{
			$a_barang[$row['idbarang1']]['jmla'] -= (float)$row['jumlah'];
			$a_barang[$row['idbarang1']]['nilaia'] -= (float)$row['total'];
		}
	}

	//bulan ini
	$sql = "select td.idbarang1, t.tok, sum(td.qty) as jumlah, sum(td.total) as total
        from aset.as_transhp t 
		join aset.as_transhpdetail td on td.idtranshp = t.idtranshp
		join aset.ms_barang1 b on b.idbarang1 = td.idbarang1
		where year(t.tgltransaksi) = '$r_tahun' and month(t.tgltransaksi) = '$r_bulan' ";

    $sql .= " group by td.idbarang1, t.tok order by td.idbarang1";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
		if($row['tok'] == 'T'){
			$a_barang[$row['idbarang1']]['jmlt'] += (float)$row['jumlah'];
			$a_barang[$row['idbarang1']]['nilait'] += (float)$row['total'];
		}else{
			$a_barang[$row['idbarang1']]['jmlk'] += (float)$row['jumlah'];
			$a_barang[$row['idbarang1']]['nilaik'] += (float)$row['total'];
		}
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
    Laporan Aktivitas Habis Pakai<br/>
    Universitas Esa Unggul<br/>
	Periode <?= $a_bulan[$r_bulan] ?> <?= $r_tahun ?>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<? /*
	<tr valign="top">
		<td width="60">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?></td>
	</tr>
	*/ ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th rowspan="2" width="30">No.</th>
	    <th rowspan="2" width="100">Kode. Barang</th>
	    <th rowspan="2">Nama Barang</th>
	    <th rowspan="2" width="75">Satuan</th>
	    <th colspan="2" width="100">Saldo Awal</th>
	    <th colspan="4" width="100">Aktivitas Per <?= $a_bulan[$r_bulan]?> <?= $r_tahun ?></th>
	    <th colspan="2" width="100">Saldo Akhir</th>
    </tr>
	<tr>
		<th width="50">Jumlah</th>
		<th width="95">Nilai</th>
		<th width="50">Tambah</th>
		<th width="95">Nilai</th>
		<th width="50">Kurang</th>
		<th width="95">Nilai</th>
		<th width="50">Jumlah</th>
		<th width="95">Nilai</th>
	</tr>
	<? 
	$i=0;
	foreach($a_barang as $idbarang1 => $val){
	    $i++;
	    $jml = $val['jmla']+$val['jmlt']-$val['jmlk'];
	    $nilai = $val['nilaia']+$val['nilait']-$val['nilaik'];
		$jmla += (float)$val['jmla'];
		$jmlt += (float)$val['jmlt'];
		$jmlk += (float)$val['jmlk'];
		$nilaia += (float)$val['nilaia'];
		$nilait += (float)$val['nilait'];
		$nilaik += (float)$val['nilaik'];
		$jmlsaldo += $jml;
		$nilaisaldo += $nilai;
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td align="center"><?= $idbarang1 ?></td>
	    <td><?= $val['namabarang'] ?></td>
	    <td><?= $val['idsatuan'] ?></td>
	    <td align="right"><?= CStr::formatNumber($val['jmla']) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['nilaia'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['jmlt']) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['nilait'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['jmlk']) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['nilaik'],2) ?></td>
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
		<td colspan="4" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmla) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilaia,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmlt) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilait,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmlk) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilaik,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmlsaldo) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilaisaldo,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
