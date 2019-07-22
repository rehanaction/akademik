<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_opname');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');

	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Opname Aset :.';	
	$p_tbwidth = 950;
	$p_ncol = 7;
	$p_namafile = 'opname_aset_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	if(!empty($r_lokasi))
    	$a_lokasi = mLaporan::getDataLokasi($conn, $r_lokasi);
	$a_param = array('unit' => $a_unit);
	$a_bulan = mCombo::bulan();
    
	$sql =" select d.iddetopname,s.noseri,
			--s.idbarang1+' - '+b.namabarang as barang,
			case when s.tglperolehan < '2016-06-01' then s.idbarang+' - '+b.namabarang else s.idbarang1+' - '+bb.namabarang END AS barang,
			d.idseri,s.merk,
			s.spesifikasi, d.idkondisi,k.kondisi,d.idstatus,t.status,s.tglperolehan 
			from aset.as_opnamedetail d 
			join aset.as_opname op on op.idopname = d.idopname
			left join aset.as_seri s on s.idseri = d.idseri 
			join aset.ms_barang b on b.idbarang = s.idbarang 
            join aset.ms_barang1 bb on bb.idbarang1 = s.idbarang1 
			left join aset.ms_kondisi k on k.idkondisi = d.idkondisi 
			left join aset.ms_status t on t.idstatus = d.idstatus 
			left join aset.ms_unit u on u.idunit = op.idunit
			left join aset.ms_lokasi l on l.idlokasi = op.idlokasi
			 where (1=1) ";
	
	if(!empty($r_unit))
		$sql .= " and op.idunit = '$r_unit' ";

    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
	
    $sql .= "order by s.noseri";
	
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
    Laporan Opname Aset<br/>
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
    <?  if(!empty($r_lokasi)){ ?>
	<tr valign="top">
		<td>Lokasi</td>
		<td>:</td>
		<td><?= $a_lokasi['idlokasi'] ?> - <?= $a_lokasi['namalokasi'] ?></td>
	</tr>
	<?  } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th width="60">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="150">Merk</th>
	    <th width="200">Spesifikasi</th>
	    <th width="100">Tgl. Perolehan</th>
	    <th width="100">Ada ?</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?= Aset::formatNoSeri($row['noseri']) ?></td>
	    <td><?=$row['barang']?></td>
	    <td><?=$row['merk']?></td>
	    <td align="center"><?=$row['spesifikasi']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
	    <td align="center"><?=$row['status']?></td>
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
