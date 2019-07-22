<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_mutasi');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	//$r_jnsrawat = CStr::removeSpecial($_REQUEST['jenisrawat']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Mutasi Barang :.';	
	$p_tbwidth = 1250;
	$p_ncol = 12;
	$p_namafile = 'mutasi_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	//$a_jnsrawat = mLaporan::getJenisRawat($conn,$r_jnsrawat);
    //$rs = mLaporan::getMutasiBarang($conn, $a_param);
	$a_bulan = mCombo::bulan();    

	$sql ="select m.tglmutasi, m.tglpengajuan, s.tglperolehan, k.kondisi,
			s.noseri, 
			case when s.tglperolehan < '2016-06-01' then s.idbarang+' - '+b.namabarang else s.idbarang1+' - '+bb.namabarang END AS barang, 
			u1.namaunit as unitasal, u2.namaunit as unittujuan, 
			m.idlokasitujuan, l.namalokasi as lokasitujuan, pg1.namalengkap as pegawaitujuan, 
			s.idlokasi, pg2.namalengkap as pegawaiasal
			from aset.as_mutasi m
			join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
			left join aset.as_seri s on s.idseri=md.idseri
			left join aset.ms_barang b on b.idbarang = s.idbarang
			left join aset.ms_barang1 bb on bb.idbarang1 = s.idbarang1
			left join aset.ms_kondisi k on k.idkondisi = s.idkondisi
			left join aset.ms_unit u1 on u1.idunit=m.idunitasal
			left join aset.ms_unit u2 on u2.idunit=m.idunittujuan
			left join aset.ms_lokasi l on l.idlokasi=m.idlokasitujuan
			left join sdm.v_biodatapegawai pg1 on pg1.idpegawai=m.idpegawaitujuan
			left join sdm.v_biodatapegawai pg2 on pg2.idpegawai=s.idpegawai
			where u1.infoleft >= {$a_unit['infoleft']} and u1.inforight <= {$a_unit['inforight']} 
			and substring(s.idbarang1,0,1) != '1'
			and datepart(year,m.tglmutasi) = '$r_tahun' and datepart(month,m.tglmutasi) between '$r_bulan1' and '$r_bulan2'
			order by b.namabarang";
	
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
    Laporan Mutasi Barang<br/>
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
	    <th width="60">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="50">Lokasi Asal</th>
	    <th width="150">Unit Asal</th>
		<th width="150">Pemakai Asal</th>
	    <th width="50">Lokasi Tujuan</th>
	    <th width="150">Unit Tujuan</th>
	    <th width="150">Pemakai Tujuan</th>
	    <th width="80">Tgl. Perolehan</th>
	    <th width="80">Kondisi</th>
	    <th width="80">Tgl. Mutasi</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?= Aset::formatNoSeri($row['noseri'])?></td>
	    <td><?=$row['barang']?></td>
	    <td><?=$row['idlokasi']?></td>
	    <td><?=$row['unitasal']?></td>
	    <td><?=$row['pegawaiasal']?></td>
	    <td><?=$row['idlokasitujuan']?></td>
	    <td><?=$row['unittujuan']?></td>
	    <td><?=$row['pegawaitujuan']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
   	    <td><?=$row['kondisi']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglmutasi'],false) ?></td>
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
