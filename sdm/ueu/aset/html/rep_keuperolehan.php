<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_keuperolehan');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
	$r_sumber = Modul::setRequest($_POST['sumber'],'SUMBER');
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Daftar Perolehan Aset :.';	
	$p_tbwidth = 1250;
	$p_ncol = 12;
	$p_namafile = 'daftar_perolehan_aset_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	$a_bulan = mCombo::bulan();
    
	$sql ="select se.noseri, p.idbarang1+' - '+b.namabarang as barang, p.merk, p.spesifikasi, 
					p.tglperolehan, p.nopo, p.tglpo, p.nobukti, pd.idlokasi+' - '+l.namalokasi as lokasi,
					pg.nip+' - '+pg.namalengkap as pemakai, k.kondisi
					from aset.as_perolehan p
					join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
					left join aset.as_seri se on se.iddetperolehan = pd.iddetperolehan
					left join aset.ms_barang1 b on b.idbarang1=p.idbarang1
					left join aset.ms_unit u on u.idunit=p.idunit
					left join aset.ms_lokasi l on l.idlokasi = pd.idlokasi
					left join sdm.v_biodatapegawai pg on pg.idpegawai = pd.idpegawai
					left join aset.ms_kondisi k on k.idkondisi = p.idkondisi
					left join aset.ms_sumberdana sd on sd.idsumberdana = p.idsumberdana
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}
					and datepart(year,p.tglbukti) = '$r_tahun' and datepart(month,p.tglbukti) = '$r_bulan'
					and p.isverify = '1' and p.nobukti is not null ";


    if(!empty($r_sumber)) 
        $sql .= "and p.idsumberdana = '$r_sumber' ";
	
	$sql .= " order by p.idbarang1 ";

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
    Laporan Daftar Perolehan Aset<br/>
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
        <th width="30">No.</th>
	    <th width="60">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="100">Merk</th>
	    <th width="150">Spesifikasi</th>
	    <th width="150">Lokasi</th>
	    <th width="150">Pemakai</th>
	    <th width="75">Tgl. PO</th>
	    <th width="80">No. PO</th>
	    <th width="80">No. Bukti</th>
	    <th width="80">Tgl. Perolehan</th>
	    <th width="50">Kondisi</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td align="center"><?= Aset::formatNoSeri($row['noseri']) ?></td>
	    <td><?=$row['barang']?></td>
	    <td><?= $row['merk']?></td>
	    <td><?= $row['spesifikasi']?></td>
	    <td><?= $row['lokasi']?></td>
	    <td><?= $row['pemakai']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglpo'],false) ?></td>
	    <td><?= $row['nopo'] ?></td>
	    <td><?= $row['nobukti'] ?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
	    <td><?= $row['kondisi'] ?></td>
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
