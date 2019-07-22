<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	Modul::getFileAuth('repp_permintaanhp');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
    $r_showchild = CStr::removeSpecial($_REQUEST['showchild']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Permintaan Habis Pakai :.';	
	$p_tbwidth = 1000;
	$p_ncol = 9;
	$p_namafile = 'rekap_permintaan_hp_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	$a_bulan = mCombo::bulan();
	//$a_barang = mLaporan::getDataBarang($conn,$r_barang);
    //$rs = mLaporan::getPermintaanHP($conn, $a_param);
    
	/*$sql ="select td.idbarang1, b.namabarang, td.idsatuan, td.qty, td.harga, t.idsumberdana, s.namasupplier, 
					t.tgltransaksi
					from aset.as_transhp t
					join aset.as_transhpdetail td on td.idtranshp=t.idtranshp
					left join aset.ms_barang1 b on b.idbarang1=td.idbarang1
					left join aset.ms_unit u on u.idunit=t.idunit
					left join aset.ms_supplier s on s.idsupplier=t.idsupplier
					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} and
					t.idjenistranshp != 306
					order by td.idbarang1";*/

	$sql = "select t.tgltransaksi, u.namaunit, td.idbarang1+' - '+b.namabarang as barang, td.idsatuan, td.qty, td.total, p.namalengkap, td.harga
			  from aset.as_transhp t
			  join aset.as_transhpdetail td on td.idtranshp = t.idtranshp
			  left join aset.ms_barang1 b on b.idbarang1 = td.idbarang1 
			  left join sdm.v_biodatapegawai p on p.idpegawai = t.idpegawai ";

    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = t.idunitaju ";
    $sql .= "where CONVERT(VARCHAR(6), t.tgltransaksi, 112) between '$r_tahun$r_bulan1' and '$r_tahun$r_bulan2' and t.tok = 'K' and isverify = '1' 
					and td.qty >= 1 and td.total >= 1 ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and t.idunitaju = '$r_unit'";
	$sql.= " order by t.tgltransaksi ";
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
    Laporan Permintaan Habis Pakai<br/>
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
	    <th width="100">Tgl. Pengambilan</th>
   	    <th width="175">Pengambil</th>
	    <th width="175">Unit</th>
	    <th>Nama Barang</th>
	    <th width="40">Jumlah</th>
	    <th width="60">Satuan</th>
	    <th width="80">Harga(Avg)</th>
   	    <th width="80">Total</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
        $nilai = (float)$row['total'];
        $total += $nilai;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?= CStr::formatDateInd($row['tgltransaksi'],false) ?></td>
   	    <td><?=$row['namalengkap']?></td>	    
	    <td><?=$row['namaunit']?></td>	    
		<td><?=$row['barang']?></td>
	    <td align="right"><?= CStr::formatNumber($row['qty']) ?></td>
	    <td><?=$row['idsatuan']?></td>
   	    <td align="right"><?= CStr::formatNumber($row['harga'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($row['total'],2) ?></td>

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
		<td colspan="8" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$total,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
