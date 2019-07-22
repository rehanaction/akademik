<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_lokasi');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
    $r_showchild = CStr::removeSpecial($_REQUEST['showchild']);

	$r_cabang = CStr::removeSpecial($_REQUEST['cabang']);
	$r_gedung = CStr::removeSpecial($_REQUEST['gedung']);
	$r_jenisruang = CStr::removeSpecial($_REQUEST['jenisruang']);

	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));

	// definisi variable halaman
	$p_title = '.: Laporan Daftar Lokasi :.';	
	$p_tbwidth = 1000;
	$p_ncol = 9;
	$p_namafile = 'daftar_lokasi_'.$r_unit.'_'.$r_lokasi;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idlokasi' => $r_lokasi, 'unit' => $a_unit);
	$a_lokasi = mLaporan::getDataLokasi($conn,$r_lokasi);

	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	if(!empty($r_cabang))
    	$a_cabang = mLaporan::getCabang($conn, $r_cabang);
	if(!empty($r_gedung))
    	$a_gedung = mLaporan::getGedung($conn, $r_gedung);

	$a_jenisruang = mCombo::jenislokasi($conn);
	
	$sql=" select l.idlokasi,l.namalokasi,j.jenislokasi,g.namagedung,l.luas,l.kapasitas, u.namaunit as unit,
			p.nip, p.namalengkap, u.kodeunit
			from aset.ms_lokasi l 
			join aset.ms_jenislokasi j on j.idjenislokasi = l.idjenislokasi 
			join aset.ms_gedung g on g.idgedung = l.idgedung 
			left join sdm.v_biodatapegawai p on p.idpegawai = l.idpetugas ";

    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = l.idunit ";
    $sql .= "where (1=1) ";
        
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
        $sql .= "and l.idunit = '$r_unit' ";

    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";

    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";

    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
	
	$sql .= " order by l.idlokasi, u.kodeunit, u.namaunit ";	

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
    Laporan Daftar Lokasi<br/>
    Universitas Esa Unggul
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
    <?  if(!empty($r_cabang)){ ?>
	<tr valign="top">
		<td>Cabang</td>
		<td>:</td>
		<td><?= $a_cabang['idcabang'] ?> - <?= $a_cabang['namacabang'] ?></td>
	</tr>
	<?  } ?>
	<?  if(!empty($r_jenisruang)){ ?>
	<tr valign="top">
		<td>Jenis Ruang</td>
		<td>:</td>
		<td><?= $a_jenisruang[$r_jenisruang] ?></td>
	</tr>
	<?  } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
	    <th width="20">No.</th>
   		<th width="200">Nama Unit</th>
	    <th width="50">ID. Lokasi</th>
	    <th width="200">Nama Lokasi</th>
 		<th>Penanggung Jawab Unit</th>
		<th width="60">Gedung</th>
		<th width="120">Jenis Lokasi</th>
		<th width="50">Luas</th>
		<th width="50">Kapasitas</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td align="center"><?= $i ?>.</td>
 		<td><?= $row['kodeunit'].' - '.$row['unit'] ?></td>
	    <td><?= $row['idlokasi'] ?></td>
	    <td><?= $row['namalokasi'] ?></td>
 		<td><?= $row['namalengkap'] ?></td>
		<td><?= $row['namagedung'] ?></td>
		<td><?= $row['jenislokasi'] ?></td>
		<td align="right"><?= $row['luas'] ?></td>
		<td align="right"><?= $row['kapasitas'] ?></td>
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
