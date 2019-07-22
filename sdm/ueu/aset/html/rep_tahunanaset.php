<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug = true;
	// hak akses
	Modul::getFileAuth('repp_tahunanaset');
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Tahunan Aset :.';	
	$p_tbwidth = 1000;
	$p_ncol = 10;
	$p_namafile = 'rekap_tahunan_aset_'.$r_unit;
	
    $a_bulan = mCombo::bulan();
	$a_barang = array();
	
	if ($r_bulan1 - 1 == 00) 
		$periodelalu = 12;
	else
		$periodelalu = str_pad(intval($r_bulan1 - 1),2,"0",STR_PAD_LEFT);
	
	if ($r_bulan1 == 01)
		$periodetahun = str_pad(intval($r_tahun - 1),2,"0",STR_PAD_LEFT);
	else
		$periodetahun = $r_tahun;
	
    //bulan saja
	$sql = "SELECT MONTH(p.tglperolehan) as bulan
			FROM aset.as_perolehanheader p
			JOIN aset.as_perolehan pd on pd.idperolehanheader = p.idperolehanheader ";
		
	$sql .="WHERE pd.isverify = 1
			AND YEAR(p.tglperolehan) < '$r_tahun' ";
	
    $sql .="GROUP BY MONTH(p.tglperolehan) ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['bulan']]['bulan'] = $row['bulan'];
	    $a_barang[$row['bulan']]['jmla'] = 0;
	    $a_barang[$row['bulan']]['nilaia'] = 0;
	    $a_barang[$row['bulan']]['jmlt'] = 0;
	    $a_barang[$row['bulan']]['jmlk'] = 0;
	    $a_barang[$row['bulan']]['nilait'] = 0;
	    $a_barang[$row['bulan']]['nilaik'] = 0;
	}

	//saldo awal
	$sql = "SELECT 
			sum(pd.qty) qtyawal, 
			sum(pd.harga) saldoawal
			FROM aset.as_perolehanheader p
			JOIN aset.as_perolehan pd on pd.idperolehanheader = p.idperolehanheader ";
	
	$sql .="WHERE pd.isverify = 1 
			AND YEAR(pd.tglperolehan) < '$r_tahun' ";

	$rs = $conn->Execute($sql);
	
	$qtyawal = 0;
	$saldoawal = 0;
	while($row = $rs->FetchRow()){
		$qtyawal = $row['qtyawal'];
		$saldoawal = $row['saldoawal'];
	}

	//saldo awal penghapusan
	$sql = "SELECT COUNT(pd.iddetpenghapusan) qtyhapus, SUM(pd.nilaipenghapusan) saldohapus
			FROM aset.as_penghapusan p 
			JOIN aset.as_penghapusandetail pd on pd.idpenghapusan = p.idpenghapusan ";
	
	$sql .="WHERE p.isok1 = 1 
			AND YEAR(p.tglpenghapusan) < '$r_tahun' ";

	$rs = $conn->Execute($sql);
	
	$qtyhapus = 0;
	$saldohapus = 0;
	while($row = $rs->FetchRow()){
		$qtyhapus = $row['qtyhapus'];
		$saldohapus = $row['saldohapus'];
	}

	//bulan ini tambah
	$sql = "SELECT MONTH(pd.tglperolehan) as bulan, SUM(pd.qty) as jumlah, SUM(pd.harga) as total 
			FROM aset.as_perolehanheader p
			JOIN aset.as_perolehan pd on pd.idperolehanheader = p.idperolehanheader ";
	
	$sql .="WHERE pd.isverify = 1
			AND YEAR(pd.tglperolehan) = '$r_tahun' ";
	
    $sql .= "GROUP BY MONTH(pd.tglperolehan)";
	
	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
		$a_barang[$row['bulan']]['jmlt'] += (float)$row['jumlah'];
		$a_barang[$row['bulan']]['nilait'] += (float)$row['total'];
	}

	//bulan ini kurang (dari penghapusan)
	$sql = "SELECT MONTH(p.tglpenghapusan) as bulan, COUNT(pd.iddetpenghapusan) as jumlah, SUM(pd.nilaipenghapusan) as total 
			FROM aset.as_penghapusan p
			JOIN aset.as_penghapusandetail pd on pd.idpenghapusan = p.idpenghapusan ";
	
	$sql .="WHERE p.isok1 = 1 
			AND YEAR(p.tglpenghapusan) = '$r_tahun' ";
	
    $sql .= "GROUP BY MONTH(p.tglpenghapusan)";
	
	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
		$a_barang[$row['bulan']]['jmlk'] += (float)$row['jumlah'];
		$a_barang[$row['bulan']]['nilaik'] += (float)$row['total'];
	}

	/*$sql = "SELECT t.tok, MONTH(t.tgltransaksi) as bulan, SUM(td.qty) as jumlah, SUM(td.total) as total
			FROM aset.as_transhp t
			JOIN aset.as_transhpdetail td on td.idtranshp = t.idtranshp ";
	
	$sql .="WHERE t.isverify = 1
			AND t.idunit = 63
			AND YEAR(t.tgltransaksi) = '$r_tahun' ";
	
    $sql .= "GROUP BY t.tok, MONTH(t.tgltransaksi)";
	
	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
		if($row['tok'] == 'T'){
			$a_barang[$row['bulan']]['jmlt'] += (float)$row['jumlah'];
			$a_barang[$row['bulan']]['nilait'] += (float)$row['total'];
		}else{
			$a_barang[$row['bulan']]['jmlk'] += (float)$row['jumlah'];
			$a_barang[$row['bulan']]['nilaik'] += (float)$row['total'];
		}
	}*/
	
	$bulan[1] = 'Januari';
	$bulan[2] = 'Pebruari';
	$bulan[3] = 'Maret';
	$bulan[4] = 'April';
	$bulan[5] = 'Mei';
	$bulan[6] = 'Juni';
	$bulan[7] = 'Juli';
	$bulan[8] = 'Agustus';
	$bulan[9] = 'September';
	$bulan[10] = 'Oktober';
	$bulan[11] = 'Nopember';
	$bulan[12] = 'Desember';
				
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
    Rekap Tahunan Aset Tetap<br/>
	Periode 
	<?= $r_tahun ?>
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
    <tr bgcolor="#CCCCCC">
        <th rowspan="3" width="30">No.</th>
	    <th rowspan="3" width="75">Bulan / Tahun</th>
	    <th colspan="2" rowspan="2" width="100">Saldo Awal</th>
	    <th colspan="4" width="100">Mutasi Tahun Anggaran <?= $r_tahun ?></th>
	    <th colspan="2" rowspan="2" width="100">Saldo Akhir</th>
    </tr>
	<tr bgcolor="#CCCCCC">
		<th colspan="2">Penambahan</th>
		<th colspan="2">Pengurangan</th>
	</tr>
	<tr bgcolor="#CCCCCC">
		<th width="50">Kuantitas</th>
		<th width="95">Nilai (Rp.)</th>
		<th width="50">Kuantitas</th>
		<th width="95">Nilai (Rp.)</th>
		<th width="50">Kuantitas</th>
		<th width="95">Nilai (Rp.)</th>
		<th width="50">Kuantitas</th>
		<th width="95">Nilai (Rp.)</th>
	</tr>
	<tr bgcolor="#CCCCCC">
		<td align="center">1</td>
		<td align="center">2</td>
		<td align="center">3</td>
		<td align="center">4</td>
		<td align="center">5</td>
		<td align="center">6</td>
		<td align="center">7</td>
		<td align="center">8</td>
		<td align="center">9</td>
		<td align="center">10</td>
	</tr>
	<? 
	$i=0;
	foreach($bulan as $bln => $val){
	    $i++;		
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td><?= $bulan[$bln] ?></td>
	    <td align="right"><?= Cstr::formatNumber($qtyawal - $qtyhapus) ?></td>
	    <td align="right"><?= Cstr::formatNumber($saldoawal - $saldohapus,2) ?></td>
	    <td align="right"><?= Cstr::formatNumber($a_barang[$bln]['jmlt']) ?></td>
	    <td align="right"><?= Cstr::formatNumber($a_barang[$bln]['nilait'],2) ?></td>
	    <td align="right"><?= Cstr::formatNumber($a_barang[$bln]['jmlk']) ?></td>
	    <td align="right"><?= Cstr::formatNumber($a_barang[$bln]['nilaik'],2) ?></td>
		<td align="right"><?= Cstr::formatNumber($qtyawal - $qtyhapus + $a_barang[$bln]['jmlt'] - $a_barang[$bln]['jmlk']) ?></td>
	    <td align="right"><?= Cstr::formatNumber($saldoawal - $saldohapus + $a_barang[$bln]['nilait'] - $a_barang[$bln]['nilaik'],2) ?></td>
	</tr>
	<?
		$qtyawal = $qtyawal + $a_barang[$bln]['jmlt'] - $a_barang[$bln]['jmlk'];
		$saldoawal = $saldoawal + $a_barang[$bln]['nilait'] - $a_barang[$bln]['nilaik'];		
	}
    if($i == 0) {
	?>
	<tr>
	    <td colspan="<?= $p_ncol ?>" align="center">-- Data tidak ditemukan --</td>
	</tr>
	<? } ?>
	<tr>
		<td colspan="2" align="right"><b>TOTAL</b>&nbsp;&nbsp;</td>
		<td align="right"><b></b></td>
		<td align="right"><b></b></td>
		<td align="right"><b></b></td>
		<td align="right"><b></b></td>
		<td align="right"><b></b></td>
		<td align="right"><b></b></td>
		<td align="right"><b></b></td>
		<td align="right"><b></b></td>
    </tr>
</table>
</div>
</body>
</html>
