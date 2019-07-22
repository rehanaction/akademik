<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_mutasihp');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	//$r_jnsrawat = CStr::removeSpecial($_REQUEST['jenisrawat']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Mutasi Habis Pakai :.';	
	$p_tbwidth = 1250;
	$p_ncol = 9;
	$p_namafile = 'mutasi_hp_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_jnsrawat = mLaporan::getJenisRawat($conn,$r_jnsrawat);
    //$rs = mLaporan::getMutasiHabisPakai($conn, $a_param);
    
	$sql ="select convert(varchar(10), m.tglmutasi, 105) as tglmutasi, convert(varchar(10), m.tglpembukuan, 105) as tglpembukuan, 
					convert(varchar(10), m.tglpengajuan, 105) as tglpengajuan, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, 
					b.namabarang, u1.namaunit as unitasal,
					u2.namaunit as unittujuan, m.idlokasitujuan, l.namalokasi as lokasitujuan, p.namadepan+' '+p.gelarbelakang as pegawaitujuan, s.idbarang1
					from aset.as_mutasi m
					join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
					left join aset.as_seri s on s.idseri=md.idseri
					left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
					left join aset.ms_unit u1 on u1.idunit=m.idunit
					left join aset.ms_unit u2 on u2.idunit=m.idunittujuan
					left join aset.ms_lokasi l on l.idlokasi=m.idlokasitujuan
					left join sdm.ms_pegawai p on p.idpegawai=m.idpegawaitujuan
					where u1.infoleft >= {$a_unit['infoleft']} and u1.inforight <= {$a_unit['inforight']} and
					substring(s.idbarang1,0,1)= '1'
					order by s.idbarang1";
	
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
    Mutasi Habis Pakai
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
	    <th width="80">ID. Barang</th>
	    <th width="60">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="150">Unit Tujuan</th>
	    <th width="200">Lokasi Tujuan</th>
	    <th width="200">Pegawai Tujuan</th>
	    <th width="80">Tgl. Pengajuan</th>
	    <th width="80">Tgl. Mutasi</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?=$row['idbarang1']?></td>
	    <td align="center"><?=$row['noseri']?></td>
	    <td><?=$row['namabarang']?></td>
	    <td align="left"><?=$row['unittujuan']?></td>
	    <td align="left"><?=$row['lokasitujuan']?></td>
	    <td align="left"><?=$row['pegawaitujuan']?></td>
	    <td align="center"><?=$row['tglpengajuan']?></td>
	    <td align="center"><?=$row['tglmutasi']?></td>
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
