<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_rekapperolehan');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	$r_sumber = Modul::setRequest($_POST['sumber'],'SUMBER');
	$r_supplier = Modul::setRequest($_POST['supplier'],'SUPPLIER');
	$r_showchild = CStr::removeSpecial($_REQUEST['showchild']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Perolehan Barang :.';	
	$p_tbwidth = 850;
	$p_ncol = 8;
	$p_namafile = 'rekap_perolehan_'.$r_tahun.'_'.$r_bulan;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	if(!empty($r_sumber))
	    $a_sumber = mLaporan::getSumberdana($conn, $r_sumber);
	if(!empty($r_supplier))
	    $a_supplier = mLaporan::getDataSupplier($conn, $r_supplier);
	$a_bulan = mCombo::bulan();
    
	$sql = "select namalengkap, jabatanstruktural from sdm.v_asetpegawai where idjstruktural = 13000 ";
	
	$data = $conn->GetRow($sql);
	
	$sql ="select p.nopo, p.idunit, u.namaunit, b.namabarang, sp.namasupplier, p.tglperolehan, count(se.idseri) as qty, sum(p.harga) as jumlah
			from aset.as_perolehan p 
			join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan 
			left join aset.as_seri se on se.iddetperolehan = pd.iddetperolehan 
			left join aset.ms_barang b on b.idbarang=p.idbarang 
			left join aset.ms_supplier sp on sp.idsupplier = p.idsupplier ";
	
	if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where (1=1) 
			and p.isverify = '1'
			and CONVERT(VARCHAR(6), p.tglperolehan, 112) between '$r_tahun$r_bulan1' and '$r_tahun$r_bulan2'";
	
	if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and p.idunit = '$r_unit' ";
	
	if(!empty($r_sumber)) 
        $sql .= "and p.idsumberdana = '$r_sumber' ";
	
	if(!empty($r_supplier)) 
        $sql .= "and p.idsupplier = '$r_supplier' ";
	
	$sql .= " group by p.nopo, b.namabarang, p.tglperolehan, sp.namasupplier, p.idunit, u.namaunit
			  order by p.tglperolehan ";
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
    Laporan Rekap Perolehan Barang<br/>
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
		<td width="90">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?></td>
	</tr>
	<? if(!empty($r_sumber)) { ?>
	<tr valign="top">
		<td>Sumber Dana</td>
		<td>:</td>
		<td><?= $a_sumber['sumberdana'] ?></td>
	</tr>
	<? } ?>
	<? if(!empty($r_supplier)) { ?>
	<tr valign="top">
		<td>Supplier</td>
		<td>:</td>
		<td><?= $a_supplier['namasupplier'] ?></td>
	</tr>
	<? } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th width="100">No. PO</th>
		<th width="125">Unit</th>
	    <th>Nama Barang</th>
	    <th width="125">Supplier</th>
	    <th width="80">Tgl. Perolehan</th>
	    <th width="50">Qty</th>
	    <th width="100">Jumlah</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
		$nilai = (float)$row['jumlah'];
		$total += $nilai;
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
		<td><?= $row['nopo']?></td>
		<td><?= $row['namaunit'] ?></td>
	    <td><?= $row['namabarang']?></td>
	    <td><?= $row['namasupplier']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
	    <td align="right"><?= CStr::formatNumber($row['qty']) ?></td>
	    <td align="right"><?= CStr::formatNumber($row['jumlah'],2) ?></td>
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
		<td colspan="7" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$total,2) ?></b></td>
    </tr>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td  style="text-align:center;" width="35%">Jakarta, &nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?><?//= str_repeat('.',40) ?></td>
		<td width="35%">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td style="text-align:center;"><u><?= $data['namalengkap'] ?></u></td>
	</tr>
	<tr>
		<td style="text-align:center;"><?= $data['jabatanstruktural'] ?></td>
	</tr>
</table>
</div>
</body>
</html>
