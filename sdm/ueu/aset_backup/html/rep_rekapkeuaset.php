<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    
	// hak akses
	Modul::getFileAuth('repp_rekapkeuaset');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
    $r_showchild = CStr::removeSpecial($_REQUEST['showchild']);

	$r_cabang = CStr::removeSpecial($_REQUEST['cabang']);
	$r_gedung = CStr::removeSpecial($_REQUEST['gedung']);
	$r_lantai = CStr::removeSpecial($_REQUEST['lantai']);
	$r_jenisruang = CStr::removeSpecial($_REQUEST['jenisruang']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	$r_coa = CStr::removeSpecial($_REQUEST['coa']);

	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Daftar Aset Akuntansi :.';	
	$p_tbwidth = 1000;
	$p_ncol = 9;
	$p_namafile = 'rekap_daftar_aset_akuntansi_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	if(!empty($r_lokasi))
    	$a_lokasi = mLaporan::getDataLokasi($conn, $r_lokasi);
	if(!empty($r_cabang))
    	$a_cabang = mLaporan::getCabang($conn, $r_cabang);
	if(!empty($r_gedung))
    	$a_gedung = mLaporan::getGedung($conn, $r_gedung);
	if(!empty($r_coa))
    	$a_coa = mLaporan::getDataCOA($conn, $r_coa);

	$a_lantai = mCombo::lantai();
	$a_jenisruang = mCombo::jenislokasi($conn);
	$a_bulan = mCombo::bulan();
	$a_coa = mCombo::coa($conn);
    
	$sql ="select count(se.idseri) jumlah, p.idbarang+' - '+b.namabarang as barang, 
		p.tglperolehan, p.nobukti, p.tglbukti, p.idcoa, u.namaunit, p.total, jp.jenispenyusutan, b.idsatuan,
		u2.kodeunit, u2.namaunit, p.harga
		from aset.as_perolehan p 
		join aset.as_perolehandetail pd on pd.idperolehan = p.idperolehan 
		left join aset.as_seri se on se.iddetperolehan = pd.iddetperolehan 
		left join aset.ms_barang b on b.idbarang = p.idbarang 
		left join aset.ms_jenispenyusutan jp on jp.idjenispenyusutan = b.idjenispenyusutan
	   left join aset.ms_lokasi l on l.idlokasi = se.idlokasi 
	   left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = se.idunit join aset.ms_unit u2 on u2.idunit = p.idunit ";
    $sql .= "where (1=1) 
			and p.nobukti is not null
			and datepart(year,p.tglbukti) = '$r_tahun' and datepart(month,p.tglbukti) between '$r_bulan1' and '$r_bulan2' ";

    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and se.idunit = '$r_unit' ";

    if(!empty($r_lokasi)) 
        $sql .= "and se.idlokasi = '$r_lokasi' ";

    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";

    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";

    if(!empty($r_lantai)) 
        $sql .= "and substring(se.idlokasi,3,1) = '$r_lantai' ";
        
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
	
	if(!empty($r_coa)) 
        $sql .= "and p.idcoa = '$r_coa' ";

	$sql .= " group by p.idbarang, b.namabarang,
		p.tglperolehan, p.nobukti, p.tglbukti, p.idcoa, u.namaunit, p.total, jp.jenispenyusutan, b.idsatuan,
		u2.kodeunit, u2.namaunit, p.harga";
	
    $sql .= " order by p.tglbukti";
	

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
    Laporan Daftar Aset Akuntansi<br/>
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
		<td width="100">Unit</td>
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
    <?  if(!empty($r_cabang)){ ?>
	<tr valign="top">
		<td>Cabang</td>
		<td>:</td>
		<td><?= $a_cabang['idcabang'] ?> - <?= $a_cabang['namacabang'] ?></td>
	</tr>
	<?  } ?>
    <?  if(!empty($r_lantai)){ ?>
	<tr valign="top">
		<td>Lantai</td>
		<td>:</td>
		<td><?= $a_lantai[$r_lantai] ?></td>
	</tr>
	<?  } ?>
	<?  if(!empty($r_jenisruang)){ ?>
	<tr valign="top">
		<td>Jenis Ruang</td>
		<td>:</td>
		<td><?= $a_jenisruang[$r_jenisruang] ?></td>
	</tr>
	<?  } ?>
	<?  if(!empty($r_coa)){ ?>
	<tr valign="top">
		<td>Kode COA</td>
		<td>:</td>
		<td><?= $a_coa[$r_coa] ?></td>
	</tr>
	<?  } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th>Nama Barang</th>
		<th width="60">Jumlah</th>
		<th width="75">Harga Satuan</th>
		<th width="60">Satuan</th>
		<th width="150">Unit</th>
		<? if(empty($r_coa)) { ?>
		<th width="60">Kode COA</th>
		<? } ?>
		<th width="80">Tgl. Bukti</th>
	    <th width="100">No. Bukti</th>
	    <th width="100">Nilai Aset</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
		$jml = (float)$row['jumlah'];
        $total = (float)$row['jumlah']*(float)$row['harga'];
        $grandtotal += $total;
	?>
	<tr valign="top">
	    <td><?= $i; ?>.</td>
	    <td><?= $row['barang'] ?></td>
		<td align="right"><?= CStr::formatNumber($row['jumlah'],2) ?></td>
		<td align="right"><?= CStr::formatNumber($row['harga'],2) ?></td>
		<td><?= $row['idsatuan'] ?></td>
		<td><?= $row['kodeunit'] ?> - <?= $row['namaunit'] ?></td>
		<? if(empty($r_coa)) {?>
		<td><?= $row['idcoa']?></td>
		<? } ?>
		<td align="center"><?= CStr::formatDateInd($row['tglbukti'],false) ?></td>
	    <td><?= $row['nobukti'] ?></td>
	    <td align="right"><?= CStr::formatNumber($row['total'],2) ?></td>
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
		<td colspan="<?= empty($r_coa) ? '9' : '8' ?>" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$grandtotal,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
