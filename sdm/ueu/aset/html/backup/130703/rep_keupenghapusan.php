<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_keupenghapusan');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	//$r_barang = CStr::removeSpecial($_REQUEST['barang']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Daftar Penghapusan Aset :.';	
	$p_tbwidth = 850;
	$p_ncol = 7;
	$p_namafile = 'daftar_penghapusan_aset_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_barang = mLaporan::getDataBarang($conn,$r_barang);
    //$rs = mLaporan::getPenghapusanAset($conn, $a_param);
    
	$sql ="select p.idpenghapusan, jp.jenispenghapusan, convert(varchar(10), p.tglpembukuan, 105) as tglpembukuan, pd.idseri, 
					b.namabarang, pd.nilaipenghapusan, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.merk, s.spesifikasi
					from aset.as_penghapusan p 
					left join aset.as_penghapusandetail pd on pd.idpenghapusan=p.idpenghapusan
					left join aset.as_seri s on s.idseri=pd.idseri
					left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
					left join aset.ms_unit u on u.idunit=p.idunit
					left join aset.ms_jenispenghapusan jp on jp.idjenispenghapusan=p.idjenispenghapusan
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} 
					order by p.idpenghapusan";
	
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
    Daftar Penghapusan Aset
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
	    <!--th width="90">ID. Penghapusan</th-->
	    <th width="100">Jenis Penghapusan</th>
	    <th width="80">Tgl. Pembukuan</th>
	    <th width="50">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="100">Merk</th>
	    <th width="150">Spesifikasi</th>
	    <th width="100">Nilai Penghapusan</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <!--td align="center"><?=$row['idpenghapusan']?></td-->
	    <td align="center"><?=$row['jenispenghapusan']?></td>
	    <td align="center"><?=$row['tglpembukuan']?></td>
	    <td align="center"><?=$row['noseri']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="left"><?=$row['merk']?></td>
	    <td align="left"><?=$row['spesifikasi']?></td>
	    <td align="right"><?=$row['nilaipenghapusan']?></td>
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
