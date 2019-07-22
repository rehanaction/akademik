<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth('repp_perolehan');
	$a_auth = Modul::getFileAuth();
	
	$s_nilai = $a_auth['canother']['N'];

	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	$r_sumber = Modul::setRequest($_POST['sumber'],'SUMBER');
	$r_supplier = Modul::setRequest($_POST['supplier'],'SUPPLIER');
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Perolehan Barang :.';	
	$p_tbwidth = 1250;
	$p_ncol = 16;
	$p_namafile = 'laporan_perolehan_'.$r_unit.'_'.$r_tahun.'_'.$r_bulan;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_supplier = mLaporan::getDataSupplier($conn, $r_supplier);
	$a_param = array('unit' => $a_unit);
	$a_bulan = mCombo::bulan();
    
	$sql ="select se.noseri, p.idbarang+' - '+b.namabarang as barang, p.merk, p.spesifikasi, 
					p.tglperolehan, p.nopo, p.tglpo, pd.idlokasi+' - '+l.namalokasi as lokasi,
					pg.namalengkap as pemakai, k.kondisi, u.namaunit, pd.idlokasi, p.idsumberdana,
					1 as qty, p.harga, p.total
					from aset.as_perolehan p
					join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
					left join aset.as_seri se on se.iddetperolehan = pd.iddetperolehan
					left join aset.ms_barang b on b.idbarang=p.idbarang
					left join aset.ms_unit u on u.idunit=p.idunit
					left join aset.ms_lokasi l on l.idlokasi = pd.idlokasi
					left join sdm.v_biodatapegawai pg on pg.idpegawai = pd.idpegawai
					left join aset.ms_kondisi k on k.idkondisi = p.idkondisi
					left join aset.ms_sumberdana sd on sd.idsumberdana = p.idsumberdana
					left join aset.ms_supplier s on s.idsupplier = p.idsupplier
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}
					and datepart(year,p.tglperolehan) = '$r_tahun' and datepart(month,p.tglperolehan) between '$r_bulan1' and '$r_bulan2'
					and p.isverify = '1' ";

    if(!empty($r_sumber)) 
        $sql .= " and p.idsumberdana = '$r_sumber' ";
	
	if(!empty($r_supplier))
		$sql .= " and p.idsupplier = '$r_supplier' ";

    $sql .= " order by p.tglperolehan ";
	


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
    Laporan Aktivitas Perolehan Barang<br/>
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
	<tr valign="top">
		<td width="60">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?></td>
	</tr>
	<? if(!empty($r_supplier)) { ?>
	<tr valign="top">
		<td width="60">Supplier</td>
		<td width="5">:</td>
		<td><?= $a_supplier['idsupplier'] ?> - <?= $a_supplier['namasupplier'] ?></td>
	</tr>
	<? } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th width="40">Lokasi</th>
	    <th width="125">Unit</th>        
	    <th width="125">Pemakai</th>
	    <th width="45">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="70">Merk</th>
	    <th width="150">Spesifikasi</th>
	    <th width="65">Tgl. PO</th>
	    <th width="80">No. PO</th>
	    <th width="65">Tgl. Perolehan</th>
		<th width="60">Sumber Dana</th>
	    <th width="50">Kondisi</th>
		<? if($s_nilai) { ?>
		<th width="40">Jml</th>
		<th width="75">Harga</th>
		<th width="75">Total</th>
		<? } ?>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
		$total = (float)$row['qty']*(float)$row['harga'];
		$tot += (float)$total;
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td><?= $row['idlokasi']?></td>
   	    <td><?= $row['namaunit']?></td>
	    <td><?= $row['pemakai']?></td>
	    <td align="center"><?= Aset::formatNoSeri($row['noseri']) ?></td>
	    <td><?=$row['barang']?></td>
	    <td><?= $row['merk']?></td>
	    <td><?= $row['spesifikasi']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglpo'],false) ?></td>
	    <td><?= $row['nopo'] ?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
	    <td><?= $row['idsumberdana'] ?></td>
		<td><?= $row['kondisi'] ?></td>
		<? if($s_nilai) { ?>
		<td align="right"><?= CStr::formatNumber($row['qty']) ?></td>
		<td align="right"><?= CStr::formatNumber($row['harga']) ?></td>
		<td align="right"><?= CStr::formatNumber($total) ?></td>
		<? } ?>
	</tr>
	<?
	}
    if($i == 0) {
	?>
	<tr>
	    <td colspan="<?= $p_ncol ?>" align="center">-- Data tidak ditemukan --</td>
	</tr>
	<? } ?>
	<? if($s_nilai) { ?>
	<tr>
		<td colspan="15" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$tot) ?></b></td>
    </tr>
	<? } ?>
</table>
</div>
</body>
</html>
