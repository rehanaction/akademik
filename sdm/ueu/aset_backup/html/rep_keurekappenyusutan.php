<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_keurekappenyusutan');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	$r_coa = CStr::removeSpecial($_REQUEST['coa']);
	$r_showchild = CStr::removeSpecial($_REQUEST['showchild']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Penyusutan Aset :.';	
	$p_tbwidth = 1200;
	$p_ncol = 14;
	$p_namafile = 'rekap_penyusutan_aset_'.$r_unit;
	
	if(!empty($r_coa))
    	$a_coa = mLaporan::getDataCOA($conn, $r_coa);
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	$a_bulan = mCombo::bulan();	
	$a_coa = mCombo::coa($conn);
    	
	$sql = " select s.idbarang, b.namabarang, b.idsatuan, count(s.idseri) jumlah, tb2.nilaisusut, tb1.akumsusut, tb2.nilaiaset, 
				jp.jenispenyusutan, p.idcoa, tb2.nilaiawal, s.idunit, u.namaunit, tb3.prevnilaiaset, tb4.prevakumsusut, p.tglbukti
				from
				(select idseri,sum(nilaisusut) akumsusut
				from aset.as_histdepresiasi hs
				where periode between '$r_tahun$r_bulan1' and '$r_tahun$r_bulan2'
				group by idseri) tb1
				join 
				(select  idseri,nilaiaset,nilaiawal,nilaisusut
				from aset.as_histdepresiasi hs
				where periode = '$r_tahun$r_bulan2') tb2
				on tb1.idseri = tb2.idseri
				left join
				(select idseri, nilaiaset as prevnilaiaset 
				from aset.as_histdepresiasi hs 
				where periode = '".str_pad(intval($r_tahun - 1),2,"0",STR_PAD_LEFT)."12' ) tb3
				on tb2.idseri = tb3.idseri
				left join
				( select idseri, sum(nilaisusut) prevakumsusut
				from aset.as_histdepresiasi hs
				where periode between '".str_pad(intval($r_tahun - 1),2,"0",STR_PAD_LEFT)."01' and '".str_pad(intval($r_tahun - 1),2,"0",STR_PAD_LEFT)."12' 
				group by idseri) tb4
				on tb3.idseri = tb4.idseri
				join aset.as_seri s on s.idseri = tb1.idseri
				join aset.ms_barang b on b.idbarang = s.idbarang
				left join aset.as_perolehandetail pd on pd.iddetperolehan = s.iddetperolehan 
				left join aset.as_perolehan p on p.idperolehan = pd.idperolehan 
				left join aset.ms_jenispenyusutan jp on jp.idjenispenyusutan = b.idjenispenyusutan ";
	
	if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			and p.nobukti is not null ";
	
	if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
	
	if(!empty($r_coa)) 
        $sql .= "and p.idcoa = '$r_coa' ";
		
	$sql .= "group by s.idbarang, b.namabarang, b.idsatuan, tb2.nilaisusut, tb1.akumsusut, tb2.nilaiaset, jp.jenispenyusutan, p.idcoa, 
			tb2.nilaiawal, s.idunit, u.namaunit, tb3.prevnilaiaset, tb4.prevakumsusut, p.tglbukti ";
	
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
    Laporan Rekap Penyusutan Aset<br/>
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
		<td width="100">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?></td>
	</tr>
	<?  if(!empty($r_coa)){ ?>
	<tr valign="top">
		<td>Kode COA</td>
		<td>:</td>
		<td><?= $a_coa[$r_coa] ?></td>
	</tr>
	<?  } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th>Barang</th>
		<th width="30">Jml</th>
		<th width="40">Satuan</th>
		<th width="100">Unit</th>
		<th width="100">Jenis Penyusutan</th>
	    <? if(empty($r_coa)) { ?>
		<th width="60">Kode COA</th>
		<? } ?>
		<th width="65">Tgl. Bukti</th>
		<th width="95">Harga Perolehan di <?= $r_tahun ?></th>
		<th width="100">Nilai Buku</br> Awal <?= $r_tahun ?></th>
		<th width="75">Akumulasi penyusutan</br> Awal <?= $r_tahun ?></th>
	    <th width="75">Penyusutan perbulan</th>
		<th width="80">Penyusutan Januari S/D bulan Desember <?= $r_tahun?></th>
		<th width="80">Akumulasi Penyusutan</br> S/D <?= $a_bulan[$r_bulan2] ?> - <?= $r_tahun ?></th>
	    <th width="90">Nilai Buku </br>Per <?= $a_bulan[$r_bulan2] ?> - <?= $r_tahun ?></th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
		$nilaiawal = (float)$row['nilaiawal'];
		$prevaset = (float)$row['prevnilaiaset'];
		$prevsusut = (float)$row['prevakumsusut'];
		$nilaisusut = (float)$row['nilaisusut'];
		$jmlsusut = (float)$row['prevakumsusut']+(float)$row['akumsusut'];
		$akumsusut = (float)$row['akumsusut'];
        $nilai = (float)$row['nilaiawal']-((float)$row['prevakumsusut']+(float)$row['akumsusut']);
		$perolehan += $nilaiawal;
		$totprevaset += $prevaset;
		$totprevsusut += $prevsusut;
		$totnilaisusut += $nilaisusut;
		$totjmlsusut += $jmlsusut;
		$totakumsusut += $akumsusut;
        $total += $nilai;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td><?= $row['idbarang'].' - '.$row['namabarang'] ?></td>
		<td align="center"><?= CStr::formatNumber($row['jumlah']) ?></td>
	    <td><?= $row['idsatuan'] ?></td>
		<td><?= $row['idunit'].' - '.$row['namaunit'] ?></td>
		<td><?= $row['jenispenyusutan'] ?></td>
		<? if(empty($r_coa)) {?>
		<td><?= $row['idcoa'] ?></td>
		<? } ?>
		<td align="center"><?= CStr::formatDateInd($row['tglbukti'],false) ?></td>
		<td align="right"><?= CStr::formatNumber($row['nilaiawal'],2) ?></td>
		<td align="right"><?= CStr::formatNumber($row['prevnilaiaset'],2) ?></td>
		<td align="right"><?= CStr::formatNumber($row['prevakumsusut'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($row['nilaisusut']*$row['jumlah'],2) ?></td>
		<td align="right"><?= CStr::formatNumber($row['akumsusut']*$row['jumlah'],2) ?></td>
		<td align="right"><?= CStr::formatNumber(($row['prevakumsusut'] + $row['akumsusut'])*$row['jumlah'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber(($row['nilaiawal'] - ($row['prevakumsusut'] + $row['akumsusut']))*$row['jumlah'],2) ?></td>
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
		<td colspan="<?= empty($r_coa) ? '8' : '7' ?>" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$perolehan,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$totprevaset,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$totprevsusut,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$totnilaisusut,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$totakumsusut,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$totjmlsusut,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$total,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
