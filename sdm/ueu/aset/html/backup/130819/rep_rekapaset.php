<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    //$conn->debug = false;	
	// hak akses
	Modul::getFileAuth('repp_rekapaset');
	
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
	$p_title = '.: Laporan Rekap Daftar Barang :.';	
	$p_tbwidth = 600;
	$p_ncol = 4;
	$p_namafile = 'rekap_barang_'.$r_unit.'_'.$r_lokasi;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	if(!empty($r_lokasi))
    	$a_lokasi = mLaporan::getDataLokasi($conn, $r_lokasi);
	if(!empty($r_cabang))
    	$a_cabang = mLaporan::getCabang($conn, $r_cabang);
	if(!empty($r_gedung))
    	$a_gedung = mLaporan::getGedung($conn, $r_gedung);

	$a_lantai = mCombo::lantai();
	$a_jenisruang = mCombo::jenislokasi($conn);
    
	$sql ="select s.idbarang1,b.namabarang,count(s.idseri) as total,sum(s.nilaiaset) as nilai
	   from aset.as_seri s 
	   join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
	   left join aset.ms_lokasi l on l.idlokasi = s.idlokasi
	   left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) ";

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

    $sql .= "group by s.idbarang1,b.namabarang order by s.idbarang1";

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
    Laporan Rekap Daftar Barang<br/>
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
	    <th width="80">Jumlah</th>
	    <th width="100">Nilai Aset</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?= $i; ?>.</td>
	    <td><?= $row['idbarang1'].' - '.$row['namabarang'] ?></td>
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
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>Penanggung Jawab Unit</td>
		<td>Pengelola Aset</td>
		<td>Mengetahui</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td></td>
		<td>Kabag. RT</td>
		<td>Ka. Dept. Umum</td>
	</tr>
</table>
</div>
</body>
</html>
