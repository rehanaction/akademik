<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_maintenance');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
//	$r_supplier = CStr::removeSpecial($_REQUEST['supplier']);
	$r_jnsrawat = CStr::removeSpecial($_REQUEST['jenisrawat']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Maintenance :.';	
	$p_tbwidth = 1000;
	$p_ncol = 10;
	$p_namafile = 'maintenance_'.$r_unit.'_'.$jnsrawat;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idjenisrawat' => $r_jnsrawat, 'unit' => $a_unit);
	$a_jnsrawat = mLaporan::getJenisRawat($conn,$r_jnsrawat);
//	$a_supplier = mCombo::supplier($conn);
    $a_bulan = mCombo::bulan();

	$sql ="select s.idlokasi, u.namaunit, p.nip+' - '+p.namalengkap as pemakai, s.noseri, 
			--s.idbarang1+' - '+b.namabarang as barang,
			case when r.inserttime < '2016-06-01' then r.idbarang+' - '+b.namabarang else r.idbarang1+' - '+bb.namabarang END AS barang,
		    r.tglrawat, r.tglkembali, sp.namasupplier, s.tglperolehan, k.kondisi, jr.jenisrawat
			from aset.as_rawat r
			join aset.as_rawatdetail rd on rd.idrawat=r.idrawat
			left join aset.ms_jenisrawat jr on jr.idjenisrawat=rd.idjenisrawat
			left join aset.as_seri s on s.idseri=rd.idseri
			--left join aset.ms_barang1 b on s.idbarang1=b.idbarang1
			left join aset.ms_barang b on b.idbarang=s.idbarang
			left join aset.ms_barang1 bb on bb.idbarang1=s.idbarang1
			left join aset.ms_unit u on u.idunit=r.idunit
			left join aset.ms_supplier sp on sp.idsupplier = r.idsupplier
			left join aset.ms_kondisi k on k.idkondisi = s.idkondisi
			left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai
			where u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']}  
			and datepart(year,r.tglrawat) = '$r_tahun' and datepart(month,r.tglrawat) = '$r_bulan'
			and substring(s.idbarang1,0,1) != '1' ";

/*    if(!empty($r_supplier)) 
        $sql .= " and r.idsupplier = '$r_supplier' ";*/

    if(!empty($r_jnsrawat)) 
        $sql .= " and jr.idjenisrawat = '$r_jnsrawat' ";
	
    $sql .= " order by s.idbarang1 ";

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
    Laporan Perawatan Aset<br/>
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
    <?/*  if(!empty($r_supplier)){ ?>
	<tr valign="top">
		<td width="100">Supplier</td>
		<td width="5">:</td>
		<td><?= $a_supplier[$r_supplier] ?></td>
	</tr>
	<? } */?>
    <?  if(!empty($r_jnsrawat)){ ?>
	<tr valign="top">
		<td width="100">Jenis Rawat</td>
		<td width="5">:</td>
		<td><?= $a_jnsrawat['jenisrawat'] ?></td>
	</tr>
	<? } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th width="50">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="60">Lokasi</th>
	    <th width="150">Unit</th>
	    <th width="150">Pemakai</th>
	    <!--th width="150">Supplier</th-->
	    <th width="75">Tgl. Perolehan</th>
	    <th width="50">Kondisi</th>
	    <th width="75">Tgl. Rawat</th>
	    <? if(empty($r_jnsrawat)) { ?>
   	    <th width="100">Jenis Rawat</th>
   	    <? } ?>
	    <th width="75">Tgl. Kembali</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td align="center"><?= Aset::formatNoSeri($row['noseri']) ?></td>
	    <td><?=$row['barang']?></td>
	    <td align="center"><?=$row['idlokasi']?></td>
	    <td><?=$row['namaunit']?></td>
	    <td><?=$row['pemakai']?></td>
	    <!--td><?=$row['namasupplier']?></td-->
   	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
  	    <td><?=$row['kondisi']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglrawat'],false) ?></td>
	    <? if(empty($r_jnsrawat)) { ?>
   	    <td><?=$row['jenisrawat']?></td>
   	    <? } ?> 
	    <td align="center"><?= CStr::formatDateInd($row['tglkembali'],false) ?></td>
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
