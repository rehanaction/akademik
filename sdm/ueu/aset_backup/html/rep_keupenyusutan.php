<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_keupenyusutan');
	$conn->debug = false;
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
	$r_coa = CStr::removeSpecial($_REQUEST['coa']);
	$r_showchild = CStr::removeSpecial($_REQUEST['showchild']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Penyusutan Aset :.';	
	$p_tbwidth = 1200;
	$p_ncol = 10;
	$p_namafile = 'penyusutan_aset_'.$r_unit;
	
	if(!empty($r_coa))
    	$a_coa = mLaporan::getDataCOA($conn, $r_coa);
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('unit' => $a_unit);
	$a_bulan = mCombo::bulan();
	$a_coa = mCombo::coa($conn);
	
	if ($r_bulan - 1 == 00) 
		$periodelalu = 12;
	else
		$periodelalu = str_pad(intval($r_bulan - 1),2,"0",STR_PAD_LEFT);
	
	if ($r_bulan == 01)
		$periodetahun = str_pad(intval($r_tahun - 1),2,"0",STR_PAD_LEFT);
	else
		$periodetahun = $r_tahun;
	
	$sql = " select s.noseri, s.idbarang+' - '+b.namabarang as barang, b.idsatuan, tb2.nilaisusut, tb1.akumsusut, tb2.nilaiaset, 
				jp.jenispenyusutan, p.idcoa, tb2.nilaiawal, u.kodeunit+' - '+u.namaunit as unit, p.tglbukti, s.tglperolehan, p.harga, 
				tb3.prevakumsusut, tb4.prevnilaiaset 
				from
				(select idseri,sum(nilaisusut) akumsusut
				from aset.as_histdepresiasi hs
				where periode <= '$r_tahun$r_bulan'
				group by idseri) tb1
				join 
				(select  idseri,nilaiaset,nilaiawal,nilaisusut,isaktif
				from aset.as_histdepresiasi hs
				where periode = '$r_tahun$r_bulan') tb2
				on tb1.idseri = tb2.idseri
				left join 
				(select idseri,sum(nilaisusut) prevakumsusut from aset.as_histdepresiasi hs 
				where periode < '$r_tahun$r_bulan' group by idseri) tb3
				on tb2.idseri = tb3.idseri
				left join
				(select idseri, nilaiaset as prevnilaiaset from aset.as_histdepresiasi hs where periode = '".$periodetahun."".$periodelalu."' ) tb4
				on tb3.idseri = tb4.idseri
				join aset.as_seri s on s.idseri = tb1.idseri
				join aset.ms_barang b on b.idbarang = s.idbarang
				left join aset.as_perolehandetail pd on pd.iddetperolehan = s.iddetperolehan 
				left join aset.as_perolehan p on p.idperolehan = pd.idperolehan 
				left join aset.ms_jenispenyusutan jp on jp.idjenispenyusutan = b.idjenispenyusutan ";
	
	if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			and p.nobukti is not null
			and tb2.isaktif = '1' ";
	
	if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
	
	if(!empty($r_coa)) 
        $sql .= "and p.idcoa = '$r_coa' ";
		
	$sql .= "group by s.noseri, s.idbarang, b.namabarang, b.idsatuan, tb2.nilaisusut, tb1.akumsusut, tb2.nilaiaset, jp.jenispenyusutan, p.idcoa, 
			tb2.nilaiawal, u.kodeunit, u.namaunit, p.tglbukti, s.tglperolehan, p.harga, tb3.prevakumsusut, tb4.prevnilaiaset
			order by s.idbarang ";
	
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
    Laporan Penyusutan Aset<br/>
    Universitas Esa Unggul<br/>
	Periode <?= $a_bulan[$r_bulan] ?> <?= $r_tahun ?>
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
	    <th>Nama Barang</th>
		<th width="45">No. Seri</th>
		<th width="40">Satuan</th>
		<th width="100">Jenis Penyusutan</th>
	    <? if(empty($r_coa)) { ?>
		<th width="60">Kode COA</th>
		<? } ?>
	    <th width="150">Unit</th>
		<th width="55">Tgl. Bukti</th>
		<th width="85">Harga Perolehan</th>
		<th width="85">Nilai Buku awal bulan</th>
		<th width="70">Akumulasi penyusutan awal bulan</th>
	    <th width="70">Penyusutan bulan <?= $a_bulan[$r_bulan] ?> - <?= $r_tahun ?></th>
		<th width="70">Akumulasi penyusutan s/d <?= $a_bulan[$r_bulan] ?> - <?= $r_tahun ?></th>
	    <th width="90">Nilai Aset</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
        $harga = (float)$row['harga'];
		$prevaset = (float)$row['prevnilaiaset'];
		$prevsusut = (float)$row['prevakumsusut'];
        $nilaisusut = (float)$row['nilaisusut'];
		$akumsusut = (float)$row['akumsusut'];
		$nilai = (float)$row['nilaiaset'];
        $perolehan += $harga;
		$totprevaset += $prevaset;
		$totprevsusut += $prevsusut;
        $totalsusut += $nilaisusut;
		$akususut += $akumsusut;
		$total += $nilai;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td><?=$row['barang']?></td>
		<td align="center"><?= Aset::formatNoSeri($row['noseri']) ?></td>
		<td><?=$row['idsatuan']?></td>
		<td><?=$row['jenispenyusutan']?></td>
	    <? if(empty($r_coa)) {?>
		<td><?=$row['idcoa']?></td>
		<? } ?>
	    <td><?=$row['unit']?></td>
		<td align="center"><?= date('M-y',strtotime($row['tglbukti'])) ?></td>
		<td align="right"><?= CStr::formatNumber($row['harga'],2) ?></td>
  	    <td align="right"><?= CStr::formatNumber($row['prevnilaiaset'],2) ?></td>
		<td align="right"><?= CStr::formatNumber($row['prevakumsusut'],2) ?></td>
		<td align="right"><?= CStr::formatNumber($row['nilaisusut'],2) ?></td>
		<td align="right"><?= CStr::formatNumber($row['akumsusut'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($row['nilaiaset'],2) ?></td>
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
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$totalsusut,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$akususut,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$total,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
