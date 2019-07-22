<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    //$conn->debug = false;	
	// hak akses
	Modul::getFileAuth('repp_keunilaiaset');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
    $r_showchild = CStr::removeSpecial($_REQUEST['showchild']);

	$r_cabang = CStr::removeSpecial($_REQUEST['cabang']);
	$r_gedung = CStr::removeSpecial($_REQUEST['gedung']);
	$r_lantai = CStr::removeSpecial($_REQUEST['lantai']);
	$r_jenisruang = CStr::removeSpecial($_REQUEST['jenisruang']);

	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Jumlah Nilai Aset :.';	
	$p_tbwidth = 650;
	$p_ncol = 5;
	$p_namafile = 'jumlah_nilai_aset_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	if(!empty($r_lokasi))
    	$a_lokasi = mLaporan::getDataLokasi($conn, $r_lokasi);
	if(!empty($r_cabang))
    	$a_cabang = mLaporan::getCabang($conn, $r_cabang);
	if(!empty($r_gedung))
    	$a_gedung = mLaporan::getGedung($conn, $r_gedung);

	$a_lantai = mCombo::lantai();
	$a_jenisruang = mCombo::jenislokasi($conn);
    
	$sql ="select s.idbarang,b.namabarang,count(s.idseri) as total,sum(s.nilaiaset) as nilai,pr.idcoa
	   from aset.as_seri s 
	   left join aset.as_perolehandetail pd on pd.iddetperolehan = s.iddetperolehan
	   left join aset.as_perolehan pr on pr.idperolehan = pd.idperolehan
	   join aset.ms_barang b on b.idbarang = s.idbarang 
	   left join aset.ms_lokasi l on l.idlokasi = s.idlokasi
	   left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) and pr.isverify = '1' and pr.nobukti is not null ";

    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";

    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";

    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";

    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";

    if(!empty($r_lantai)) 
        $sql .= "and substring(s.idlokasi,3,1) = '$r_lantai' ";
        
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";

    $sql .= "group by s.idbarang,b.namabarang,pr.idcoa order by s.idbarang";

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
    Laporan Jumlah Nilai Aset<br/>
    Universitas Esa Unggul<br/>
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
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th>Barang</th>
	    <th width="80">Kode COA</th>
	    <th width="80">Jumlah</th>
	    <th width="100">Nilai Aset</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
        $nilai = (float)$row['nilai'];
        $grandtotal += $nilai;
	?>
	<tr valign="top">
	    <td><?= $i; ?>.</td>
	    <td><?= $row['idbarang'].' - '.$row['namabarang'] ?></td>
	    <td><?= $row['idcoa'] ?></td>
	    <td align="right"><?= CStr::formatNumber($row['total']) ?></td>
	    <td align="right"><?= CStr::formatNumber($row['nilai'],2) ?></td>
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
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$grandtotal,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
