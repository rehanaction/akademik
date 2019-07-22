<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_rekapstatusbarang');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_status = CStr::removeSpecial($_REQUEST['status']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Status Barang :.';	
	$p_tbwidth = 400;
	$p_ncol = 3;
	$p_namafile = 'rekap_status_barang_'.$r_unit.'_'.$status;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idstatus' => $r_status, 'unit' => $a_unit);
	$a_status = mLaporan::getDataStatus($conn,$r_status);
    //$rs = mLaporan::getRekapStatusBarang($conn, $a_param);
    
	$sql ="select s.idbarang, b.namabarang
					from aset.as_seri s 
					left join aset.ms_barang b on b.idbarang=s.idbarang 
					left join aset.ms_status st on st.idstatus=s.idstatus
					left join aset.ms_unit u on u.idunit=s.idunit
					where st.idstatus = '$r_status' and
					u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} 
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
    Rekap Status Barang
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
		<td width="100">Status Barang</td>
		<td width="5">:</td>
		<td><?= $a_status['idstatus'] ?> - <?= $a_status['status'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th width="100">ID. Barang</th>
	    <th>Nama Barang</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?=$row['idbarang']?></td>
	    <td><?=$row['namabarang']?></td>
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
