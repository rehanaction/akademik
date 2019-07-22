<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_keupenghapusan');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Daftar Penghapusan Aset :.';	
	$p_tbwidth = 1150;
	$p_ncol = 12;
	$p_namafile = 'daftar_penghapusan_aset_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	$a_bulan = mCombo::bulan();
    
	$sql ="select se.noseri, se.idbarang+' - '+b.namabarang as barang, se.merk, se.spesifikasi, 
		se.tglperolehan, p.tglpenghapusan, se.idlokasi+' - '+l.namalokasi as lokasi,se.idlokasi,
		k.kondisi, jp.jenispenghapusan, p.nobukti
		from aset.as_penghapusan p
		join aset.as_penghapusandetail pd on pd.idpenghapusan = p.idpenghapusan
		left join aset.as_seri se on se.idseri = pd.idseri
		left join aset.ms_barang b on b.idbarang=se.idbarang
		left join aset.ms_unit u on u.idunit=p.idunit
		left join aset.ms_lokasi l on l.idlokasi = se.idlokasi
		left join aset.ms_kondisi k on k.idkondisi = se.idkondisi
		left join aset.ms_jenispenghapusan jp on jp.idjenispenghapusan = p.idjenispenghapusan
		where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}
		and datepart(year,p.tglbukti) = '$r_tahun' and datepart(month,p.tglbukti) = '$r_bulan'
		and p.isok1 = '1' 
		and p.nobukti is not null
		order by se.idbarang";
	
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
    Laporan Daftar Penghapusan Aset<br/>
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
	    <th width="125">Merk</th>
	    <th width="175">Spesifikasi</th>
	    <th width="60">Lokasi</th>
	    <th width="140">Jenis Penghapusan</th>
	    <th width="90">Tgl. Penghapusan</th>
	    <th width="90">Tgl. Perolehan</th>
	    <th width="80">No. Bukti</th>	    
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
	    <td><?= $row['idlokasi']?></td>
	    <td><?= $row['jenispenghapusan'] ?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglpenghapusan'],false) ?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
	    <td><?= $row['nobukti'] ?></td>
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
