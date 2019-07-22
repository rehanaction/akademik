<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_rekapstatusbarang');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
    $r_showchild = CStr::removeSpecial($_REQUEST['showchild']);

	$r_cabang = CStr::removeSpecial($_REQUEST['cabang']);
	$r_gedung = CStr::removeSpecial($_REQUEST['gedung']);
	$r_lantai = CStr::removeSpecial($_REQUEST['lantai']);
	$r_level = CStr::removeSpecial($_REQUEST['level']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Status Barang :.';	
	$p_tbwidth = 900;
	$p_ncol = 7;
	$p_namafile = 'rekap_status_barang_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	if(!empty($r_lokasi))
    	$a_lokasi = mLaporan::getDataLokasi($conn, $r_lokasi);
	if(!empty($r_cabang))
    	$a_cabang = mLaporan::getCabang($conn, $r_cabang);
	if(!empty($r_gedung))
    	$a_gedung = mLaporan::getGedung($conn, $r_gedung);

	$a_lantai = mCombo::lantai();
	$a_status = mCombo::status($conn);
	$a_level = mCombo::level($conn);
	$a_bulan = mCombo::bulan();

	/**	Pengecekan Query **/
	if($r_level == '1'){		/*Kelompok Level 1*/
	$sql ="select substring(s.idbarang,1,1) as idbarang, b.namabarang, count(s.idseri) as total
			from aset.as_seri s 
			join aset.ms_barang b on substring(s.idbarang,1,1)+'000000000' = b.idbarang
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang,1,1) = substring(s.idbarang,1,1) ";
    $sql .= "group by substring(s.idbarang,1,1),b.namabarang order by substring(s.idbarang,1,1)";
    
    $rs = $conn->Execute($sql);
    while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']]['namabarang'] = $row['namabarang'];
        $a_barang[$row['idbarang']]['total'] = (int)$row['total'];
    }
	$sql ="select substring(s.idbarang,1,1) as idbarang, count(s.idseri) as total,s.idstatus
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang,1,1) = substring(s.idbarang,1,1) ";
    $sql .= "group by substring(s.idbarang,1,1),s.idstatus order by substring(s.idbarang,1,1) ";
    
    $rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']][$row['idstatus']] = (int)$row['total'];
    }
	} else if($r_level == '2'){		/*Kelompok Level 2*/
	$sql ="select substring(s.idbarang,1,3) as idbarang, b.namabarang, count(s.idseri) as total
			from aset.as_seri s 
			join aset.ms_barang b on substring(s.idbarang,1,3)+'0000000' = b.idbarang
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang,1,3) = substring(s.idbarang,1,3) ";
    $sql .= "group by substring(s.idbarang,1,3),b.namabarang order by substring(s.idbarang,1,3)";
    
    $rs = $conn->Execute($sql);
    while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']]['namabarang'] = $row['namabarang'];
        $a_barang[$row['idbarang']]['total'] = (int)$row['total'];
    }

	$sql ="select substring(s.idbarang,1,3) as idbarang, count(s.idseri) as total,s.idstatus
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang,1,3) = substring(s.idbarang,1,3) ";
    $sql .= "group by substring(s.idbarang,1,3),s.idstatus order by substring(s.idbarang,1,3) ";
    
    $rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']][$row['idstatus']] = (int)$row['total'];
    }
	} else if($r_level == '3'){		/*Kelompok Level 3*/
	$sql ="select substring(s.idbarang,1,5) as idbarang, b.namabarang, count(s.idseri) as total
			from aset.as_seri s 
			join aset.ms_barang b on substring(s.idbarang,1,5)+'00000' = b.idbarang
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang,1,5) = substring(s.idbarang,1,5) ";
    $sql .= "group by substring(s.idbarang,1,5),b.namabarang order by substring(s.idbarang,1,5)";
    
    $rs = $conn->Execute($sql);
    while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']]['namabarang'] = $row['namabarang'];
        $a_barang[$row['idbarang']]['total'] = (int)$row['total'];
    }

	$sql ="select substring(s.idbarang,1,5) as idbarang, count(s.idseri) as total,s.idstatus
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang,1,5) = substring(s.idbarang,1,5) ";
    $sql .= "group by substring(s.idbarang,1,5),s.idstatus order by substring(s.idbarang,1,5) ";
    
    $rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']][$row['idstatus']] = (int)$row['total'];
    }
	} else if($r_level == '4'){	/*Kelompok Level 4*/
	$sql ="select substring(s.idbarang,1,7) as idbarang, b.namabarang, count(s.idseri) as total
			from aset.as_seri s 
			join aset.ms_barang b on substring(s.idbarang,1,7)+'000' = b.idbarang
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang,1,7) = substring(s.idbarang,1,7) ";
    $sql .= "group by substring(s.idbarang,1,7),b.namabarang order by substring(s.idbarang,1,7)";
    
    $rs = $conn->Execute($sql);
    while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']]['namabarang'] = $row['namabarang'];
        $a_barang[$row['idbarang']]['total'] = (int)$row['total'];
    }

	$sql ="select substring(s.idbarang,1,7) as idbarang, count(s.idseri) as total,s.idstatus
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang,1,7) = substring(s.idbarang,1,7) ";
    $sql .= "group by substring(s.idbarang,1,7),s.idstatus order by substring(s.idbarang,1,7) ";
    
    $rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']][$row['idstatus']] = (int)$row['total'];
    }
	} else if($r_level == '5' or empty($r_level)){	/*Kelompok Level 5*/
	$sql ="select s.idbarang as idbarang, b.namabarang, count(s.idseri) as total
			from aset.as_seri s 
			join aset.ms_barang b on s.idbarang = b.idbarang
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and s.idbarang = s.idbarang ";
	$sql .= "group by s.idbarang,b.namabarang order by s.idbarang";
    
    $rs = $conn->Execute($sql);
    while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']]['namabarang'] = $row['namabarang'];
        $a_barang[$row['idbarang']]['total'] = (int)$row['total'];
    }

	$sql ="select s.idbarang as idbarang, count(s.idseri) as total,s.idstatus
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
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
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_level)) 
        $sql .= "and s.idbarang = s.idbarang ";
    $sql .= "group by s.idbarang,s.idstatus order by s.idbarang ";
    
    $rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang']][$row['idstatus']] = (int)$row['total'];
    }
	}
	/**	Akhir Pengecekan Query **/

    //print_r($a_status);
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
    Laporan Rekap Status Barang<br/>
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
	<?  if(!empty($r_level)){ ?>
	<tr valign="top">
		<td>Level Barang</td>
		<td>:</td>
		<td><?= $a_level[$r_level] ?></td>
	</tr>
	<?  } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th rowspan="2" width="30">No.</th>
	    <th rowspan="2" width="250">Barang</th>
	    <th rowspan="2" width="50">Jumlah</th>
	    <th colspan="<?= count($a_status) ?>" width="80">Status</th>
    </tr>
    <tr>
	    <?  foreach($a_status as $idstatus => $namastatus){ ?>
	    <th width="70"><?= $namastatus ?></th>
	    <?  } ?>
    </tr>
	<? 
	$i=0;
	foreach ($a_barang as $idbarang => $val){
	    $i++;
		$jml += $val['total'];
	?>
	<tr valign="top">
	    <td><?= $i; ?>.</td>
	    <td><?= Aset::formatLevelBarang($idbarang).' - '.$val['namabarang'] ?></td>
	    <td align="right"><?= CStr::formatNumber($val['total']) ?></td>
	    <?  foreach($a_status as $idstatus => $namastatus){
			$status[$idstatus] += $a_barang[$idbarang][$idstatus];
		?>
	    <td align="right"><?= CStr::formatNumber($a_barang[$idbarang][$idstatus]) ?></td>
	    <?  } ?>
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
		<td colspan="2" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jml) ?></b></td>
	    <?  foreach($a_status as $idstatus => $namastatus){ ?>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$status[$idstatus]) ?></b></td>
	    <?  } ?>
	</tr>
</table>
</div>
</body>
</html>
