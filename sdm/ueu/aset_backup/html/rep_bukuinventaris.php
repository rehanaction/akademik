<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_bukuinventaris');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_coa = CStr::removeSpecial($_REQUEST['coa']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Buku Inventaris :.';	
	$p_tbwidth = 1200;
	$p_ncol = 13;
	$p_namafile = 'buku_inventaris_'.$r_unit;
	
	//$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_coa = mLaporan::getDataCOA($conn, $r_coa);
	$a_param = array('unit' => $a_unit);
    $a_bulan = mCombo::bulan();
    
	$sql ="select p.idcoa, p.nobukti, p.tglpembukuan, b.namabarang, p.idbarang, p.merk, p.idcoa,
					p.idsumberdana, pd.qty, p.harga, isnull((pd.qty*p.harga),0) as jumlah, p.catatan, pd.idlokasi
					from aset.as_perolehan p
					join aset.as_perolehandetail pd on pd.idperolehan=p.idperolehan
					left join aset.ms_barang b on b.idbarang=p.idbarang
					where datepart(year,p.tglpembukuan) = '$r_tahun' and datepart(month,p.tglpembukuan) between '$r_bulan1' and '$r_bulan2' ";
	
	if(!empty($r_coa)){
		$sql .= " and p.idcoa = '$r_coa' ";
	}

	$sql .= " group by p.idcoa, p.nobukti, p.tglpembukuan, b.namabarang, p.idbarang, p.merk, p.idsumberdana, pd.qty,
				p.harga, pd.qty,p.harga, p.catatan, pd.idlokasi
			  order by p.nobukti ";

	$rs = $conn->Execute($sql);
	//					where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}
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
    Laporan Buku Inventaris<br/>
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
	<? /*
	<tr valign="top">
		<td width="60">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?></td>
	</tr>
	*/ ?>
	<tr valign="top">
		<td width="60"><b>Kode COA</b></td>
		<td width="5">:</td>
		<td><b><?= $a_coa['idcoa'] ?> - <?= $a_coa['namacoa'] ?></b></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <!--<tr>
        <th width="30">No.</th>
	    <th width="100">No. Bukti</th>
	    <th width="80">Tgl. Pembukuan</th>
	    <th>Nama Barang</th>
	    <th width="100">Kode Barang</th>
	    <th width="100">Merk / Type</th>
	    <th width="60">Sumber Dana</th>
	    <th width="50">Jumlah Barang</th>
	    <th width="75">Harga</th>
	    <th width="75">Jumlah</th>
	    <th width="150">Keterangan</th>
	    <th width="50">Kode Lokasi</th>
    </tr>-->
	<? 
	$i=0;
    $kdbarang = null;
    $jumlah = 0;
    $totharga = 0;
	while ($row = $rs->FetchRow ()){
        if(!is_null($kdbarang) and $kdbarang <> $row['idbarang']){
            echo "<tr>
                    <td colspan=\"3\"></td>
                    <td colspan=\"4\">JUMLAH ..........</td>
                    <td align=\"right\">$jumlah</td>
                    <td></td>
                    <td align=\"right\">$totharga</td>
                    <td></td>
                    <td colspan=\"3\"></td>
                  </tr>";
            echo "<tr>
                    <td colspan=\"12\" style=\"border:none;height:20px;\"></td>
                  </tr>";    
        }
        if(is_null($kdbarang) or $kdbarang <> $row['idbarang']){
	        echo "<tr>
                    <th width=\"30\">No.</th>
                    <th width=\"100\">No. Bukti</th>
                    <th width=\"80\">Tgl. Pembukuan</th>
                    <th>Nama Barang</th>
                    <th width=\"100\">Kode Barang</th>
                    <th width=\"100\">Merk / Type</th>
                    <th width=\"60\">Sumber Dana</th>
                    <th width=\"50\">Jumlah Barang</th>
                    <th width=\"75\">Harga</th>
                    <th width=\"75\">Jumlah</th>
                    <th width=\"150\">Keterangan</th>
                    <th width=\"50\">Kode Lokasi</th>
                </tr>";
            $kdbarang = $row['idbarang'];
            $i = 0;
            $jumlah = 0;
            $totharga = 0;
        }
        echo "<tr>
                <td>".++$i."</td>
                <td>$row[nobukti]</td>
                <td>$row[tglpembukuan]</td>
                <td>$row[namabarang]</td>
                <td>$row[idbarang]</td>
                <td>$row[merk]</td>
                <td>$row[idsumberdana]</td>
                <td align=\"right\">$row[qty]</td>
                <td align=\"right\">$row[harga]</td>
                <td align=\"right\">$row[jumlah]</td>
                <td>$row[catatan]</td>
                <td>$row[idlokasi]</td>
            </tr>";
        $jumlah += CStr::formatNumber($row['qty']);
        $totharga += CStr::formatNumber($row['jumlah'],2);
    }
    ?>
	<!--<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td align="center"><?= $row['nobukti']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglpembukuan']) ?></td>
	    <td><?= $row['namabarang']?></td>
	    <td align="center"><?= $row['idbarang']?></td>
	    <td align="left"><?= $row['merk']?></td>
	    <td align="center"><?= $row['idsumberdana']?></td>
	    <td align="right"><?= CStr::formatNumber($row['qty']) ?></td>
	    <td align="right"><?= CStr::formatNumber($row['harga'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($row['jumlah'],2) ?></td>
	    <td align="left"><?= $row['catatan']?></td>
	    <td align="center"><?= $row['idlokasi']?></td>
	</tr>-->
    
	<?
	//}
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
