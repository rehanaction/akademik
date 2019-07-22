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
	$p_tbwidth = 750;
	$p_ncol = 6;
	$p_namafile = 'maintenance_jatuh_tempo_'.$r_unit.'_'.$jnsrawat;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idjenisrawat' => $r_jnsrawat, 'unit' => $a_unit);
	$a_jnsrawat = mLaporan::getJenisRawat($conn,$r_jnsrawat);
    //$rs = mLaporan::getMaintenanceJatuhTempo($conn, $a_param);
    
	$sql ="select s.idbarang, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, b.namabarang, convert(varchar(10), r.tglrawat, 105) as tglrawat, 
					convert(varchar(10), r.tglkembali, 105) as tglkembali
					from aset.as_rawat r
					join aset.as_rawatdetail rd on rd.idrawat=r.idrawat
					left join aset.ms_jenisrawat jr on jr.idjenisrawat=r.idjenisrawat
					left join aset.as_seri s on s.idseri=rd.idseri
					left join aset.ms_barang b on s.idbarang=b.idbarang
					left join aset.ms_unit u on u.idunit=r.idunit
					where jr.idjenisrawat = '$r_jnsrawat' and
					u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} and 
					substring(s.idbarang,0,1) != '1'
					order by s.idbarang";
	
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
    Universitas Esa Unggul<br/>
    Maintenance Jatuh Tempo
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
	<tr valign="top">
		<td width="100">Jenis Rawat</td>
		<td width="5">:</td>
		<td><?= $a_jnsrawat['jenisrawat'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th width="100">ID. Barang</th>
	    <th width="80">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="100">Tgl. Rawat</th>
	    <th width="100">Tgl. Kembali</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?=$row['idbarang']?></td>
	    <td align="center"><?=$row['noseri']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="center"><?=$row['tglrawat']?></td>
	    <td align="center"><?=$row['tglkembali']?></td>
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
