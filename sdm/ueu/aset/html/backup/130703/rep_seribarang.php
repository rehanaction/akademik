<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_seribarang');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
    $r_showchild = CStr::removeSpecial($_REQUEST['showchild']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));

	// definisi variable halaman
	$p_title = '.: Laporan Daftar Seri Barang :.';	
	$p_tbwidth = 750;
	$p_ncol = 5;
	$p_namafile = 'daftar_seri_barang_'.$r_unit.'_'.$r_lokasi;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idlokasi' => $r_lokasi, 'unit' => $a_unit);
	$a_lokasi = mLaporan::getDataLokasi($conn,$r_lokasi);
    //$rs = mLaporan::getSeriBarang($conn, $a_param);
    
	/*$sql ="select s.noseri, s.idbarang1+' - '+b.namabarang as barang, 
					s.tglperolehan, l.namalokasi, u.namaunit, p.namalengkap, k.kondisi
					from aset.as_seri s
					left join aset.ms_barang1 b on b.idbarang1=s.idbarang1
					left join aset.ms_lokasi l on l.idlokasi=s.idlokasi
					left join aset.ms_unit u on u.idunit=s.idunit
					left join aset.ms_kondisi k on k.idkondisi = s.idkondisi
					left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai
					where s.idlokasi = '$r_lokasi' and
					u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} 
					order by s.noseri";
	
	$rs = $conn->Execute($sql);*/
	
	$sql="select s.idbarang1,b.namabarang as namabarang, l.namalokasi as namalokasi, 
		s.noseri, s.idkondisi, s.idstatus, s.merk, 
		s.spesifikasi, s.tglperolehan, s.idlokasi, s.idunit, p.namalengkap, k.kondisi, st.status
		from aset.as_seri s
		left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1
		left join aset.ms_lokasi l on l.idlokasi = s.idlokasi
		left join sdm.v_biodatapegawai p on s.idpegawai = p.idpegawai
		left join aset.ms_kondisi k on k.idkondisi = s.idkondisi
		left join aset.ms_status st on st.idstatus = s.idstatus ";
		
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) ";
        
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
        $sql .= "and s.idunit = '$r_unit' ";
        
    if(!empty($r_lokasi)) 
        $sql .= " and s.idlokasi = '$r_lokasi' ";
    $sql .= "group by s.idbarang1,b.namabarang,l.namalokasi,s.noseri, s.idkondisi, s.idstatus, s.merk,
             s.spesifikasi, s.tglperolehan, s.idlokasi, s.idunit, p.namalengkap, k.kondisi, st.status
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
    Daftar Seri Barang
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
	<tr valign="top">
		<td width="60">Lokasi</td>
		<td width="5">:</td>
		<td><?= $a_param['idlokasi'] ?> - <?= $a_lokasi['namalokasi'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <!--th width="30">No.</th>
	    <th width="80">No. Seri</th>
	    <th>Nama Barang</th>
	    <th width="80">Tgl. Perolehan</th>
	    <th width="225">Penanggung Jawab Unit</th>
	    <th width="80">Kondisi</th-->
	    <th width="20">No.</th>
		<th width="50">No. Seri</th>
		<th>Nama Barang</th>
		<th width="40">Merk</th>
		<th width="75">Tgl. Perolehan</th>
		<th>Pemakai</th>
		<th width="70">ID. Lokasi</th>
		<th width="60">Kondisi</th>
		<th width="60">Status</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <!--td align="center"><?=$i;?>.</td>
	    <td align="center"><?= Aset::setFormatNoSeri($row['noseri']) ?></td>
	    <td><?=$row['barang']?></td>
	    <td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
	    <td><?=$row['namalengkap']?></td>
	    <td><?=$row['kondisi']?></td-->
	    <td align="center"><?= $i ?>.</td>
		<td align="center"><?= Aset::formatNoSeri($row['noseri']) ?></td>
		<td><?= $row['namabarang'] ?></td>
		<td><?= $row['merk'] ?></td>
		<td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
		<td><?= $row['namalengkap'] ?></td>
		<td><?= $row['idlokasi'] ?></td>
		<td align="center"><?= $row['kondisi'] ?></td>
		<td align="center"><?= $row['status'] ?></td>
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
