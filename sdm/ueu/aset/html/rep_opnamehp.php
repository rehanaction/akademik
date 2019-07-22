<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_opnamehp');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Opname Habis Pakai :.';	
	$p_tbwidth = 800;
	$p_ncol = 8;
	$p_namafile = 'opname_hp_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_jnsrawat = mLaporan::getJenisRawat($conn,$r_jnsrawat);
    //$rs = mLaporan::getOpnameHP($conn, $a_param);
	$a_bulan = mCombo::bulan();
    
	$sql ="select od.idbarang1, b.namabarang, convert(varchar(10), o.tglopname, 105) as tglopname, 
					convert(varchar(10), o.tglpembukuan, 105) as tglpembukuan, od.qtyawal, od.qtyakhir, od.idsatuan
					from aset.as_opnamehp o
					join aset.as_opnamehpdetail od on od.idopnamehp=o.idopnamehp
					left join aset.ms_barang1 b on od.idbarang1=b.idbarang1
					left join aset.ms_satuan s on s.idsatuan=od.idsatuan
					left join aset.ms_unit u on u.idunit=o.idunit
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} and
					substring(od.idbarang1,0,1)= '1'
					order by od.idbarang1";
	
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
    Laporan Opname Habis Pakai<br/>
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
	    <th width="100">ID. Barang</th>
	    <th>Nama Barang</th>
	    <th width="60">Qty. Awal</th>
	    <th width="60">Qty. Akhir</th>
	    <th width="80">Satuan</th>
	    <th width="100">Tgl. Opname</th>
	    <th width="100">Tgl. Pembukuan</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?=$row['idbarang1']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="right"><?=$row['qtyawal']?></td>
	    <td align="right"><?=$row['qtyakhir']?></td>
	    <td align="center"><?=$row['idsatuan']?></td>
	    <td align="center"><?=$row['tglopname']?></td>
	    <td align="center"><?=$row['tglpembukuan']?></td>
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
