<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_maintenancejatuhtempo');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_jnsrawat = CStr::removeSpecial($_REQUEST['jenisrawat']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Maintenance Jatuh Tempo :.';	
	$p_tbwidth = 1100;
	$p_ncol = 9;
	$p_namafile = 'maintenance_jatuh_tempo_'.$r_unit.'_'.$jnsrawat;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idjenisrawat' => $r_jnsrawat, 'unit' => $a_unit);
	$a_jnsrawat = mLaporan::getJenisRawat($conn,$r_jnsrawat);
	
	$sql = "select s.noseri,
			case when s.tglperolehan < '2016-06-01' then s.idbarang+' - '+b.namabarang else s.idbarang1+' - '+bb.namabarang END AS barang,
			--s.idbarang1+' - '+b.namabarang as barang,
			s.merk,s.spesifikasi,s.idlokasi,s.tglperolehan,j.periode,j.satuanperiode,
	        jr.jenisrawat,u.kodeunit+' - '+u.namaunit as unit,kk.kmpakai,
            case 
	            when j.satuanperiode = 'Bulan' and (month(s.tglperolehan)-month(getdate())%j.periode) = 0 then 1 
	            when j.satuanperiode = 'Tahun' and (year(s.tglperolehan)-year(getdate())%j.periode) = 0 then 1
	            when j.satuanperiode = 'Hari' and (day(s.tglperolehan)-day(getdate())%j.periode) = 0 then 1
				when j.satuanperiode = 'Km' and kk.kmpakai >= j.periode then 1 
            else 0 end as israwat
            from aset.as_seri s 
            join aset.ms_barang b on b.idbarang = s.idbarang 
            join aset.ms_barang1 bb on bb.idbarang1 = s.idbarang1 
            join aset.ms_jadwalrawat j on j.idbarang = b.idbarang
            join aset.ms_jadwalrawat k on k.idbarang1 = bb.idbarang1
            join aset.ms_unit u on u.idunit = s.idunit
            join aset.ms_jenisrawat jr on jr.idjenisrawat = j.idjenisrawat
			left join aset.as_kibkendaraan kk on kk.idseri = s.idseri
            where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}   ";
	
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
	Laporan Maintenance Jatuh Tempo<br/>
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
    <? /*
	<tr valign="top">
		<td width="100">Jenis Rawat</td>
		<td width="5">:</td>
		<td><?= $a_jnsrawat['jenisrawat'] ?></td>
	</tr> */ ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
        <th width="50">ID. Lokasi</th>
        <th width="200">Nama Unit</th>
	    <th width="50">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="75">Merk</th>
	    <th width="175">Spesifikasi</th>
	    <th width="75">Tgl. Perolehan</th>
	    <th width="40">Periode</th>
	    <th width="40">Satuan</th>
	    <th width="125">Jenis Rawat</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
        <td><?=$row['idlokasi']?></td>
        <td><?=$row['unit']?></td>
	    <td align="center"><?= Aset::formatNoSeri($row['noseri']) ?></td>
	    <td><?=$row['barang']?></td>
        <td><?=$row['merk']?></td>
        <td><?=$row['spesifikasi']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
	    <td align="right"><?=$row['periode']?></td>
	    <td><?=$row['satuanperiode']?></td>
		<td><?=$row['jenisrawat']?></td>
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
