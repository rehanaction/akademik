<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('list_dir');
	$conn->debug = false;
	// include
	require_once(Route::getModelPath('laporan'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_format = $_REQUEST['format'];
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
	$r_pegawai = CStr::removeSpecial($_REQUEST['pemakai']);
	
	if(!empty($r_key))
	
	// properti halaman
	$p_title = 'Kartu Inventaris Ruang';
	$p_tbwidth = 650;
	$p_ncol = 7;
	$p_namafile = 'kartu_inventaris_ruang_'.$r_key;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idlokasi' => $r_lokasi, 'unit' => $a_unit);
	$a_lokasi = mLaporan::getDataLokasi($conn,$r_lokasi);
	//$rs = mLaporan::getKIR($conn,$a_param);
	$a_pegawai = mLaporan::getPegawai($conn,$r_pegawai);
	$a_petugas = mLaporan::getPetugasRuang($conn,$r_lokasi);
	
	$sql="select s.idseri, s.idbarang, b.namabarang, l.namalokasi, u.namaunit, 
		s.noseri, s.idkondisi, s.idstatus, s.merk, 
		s.spesifikasi, s.tglperolehan, s.idlokasi, s.idunit, p.namalengkap, k.kondisi, st.status
		from aset.as_seri s
		left join aset.ms_barang b on b.idbarang = s.idbarang
		left join aset.ms_lokasi l on l.idlokasi = s.idlokasi
		left join aset.ms_unit u on u.idunit = s.idunit
		left join sdm.v_biodatapegawai p on s.idpegawai = p.idpegawai
		left join aset.ms_kondisi k on k.idkondisi = s.idkondisi
		left join aset.ms_status st on st.idstatus = s.idstatus
		where (1=1) ";
	if(!empty($r_lokasi))
		$sql .= "and s.idlokasi = '$r_lokasi' ";
	if(!empty($r_pegawai))
    	$sql .= "and s.idpegawai = '$r_pegawai' ";
    $sql .= "and u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} order by b.namabarang,s.idseri";
	
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
	<script>
	</script>
</head>
<body>
<div align="center">
<?php
    include('inc_headerlap.php');
?>
<div class="div_head">
    </br>KARTU INVENTARIS RUANG (KIR)<br/>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr valign="top">
		<td width="60">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?> ( <?= $a_param['idlokasi'] ?> )</td>
	</tr>
    <? if(!empty($r_pegawai)) { ?>
	<tr valign="top">
		<td width="60">Pemakai</td>
		<td width="5">:</td>
		<td><?= $a_pegawai['nip'] ?> - <?= $a_pegawai['pegawai'] ?></td>
	</tr>
	<?  } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No.</th>
		<th width="50">No. Seri</th>
		<th>Nama Barang</th>
		<th width="100">Merk</th>
		<th width="80">Tgl. Perolehan</th>
		<? if(empty($r_pegawai)) { ?>
		<th width="150">Pemakai</th>		
		<? } ?>
		<th width="50">Kondisi</th>
		<th width="50">Status</th>
	</tr>
    <?php

        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
    ?>
	<tr valign="top" height="20">
		<td align="center"><?= $i ?>.</td>
		<td align="center"><?= Aset::formatNoSeri($row['noseri']) ?></td>
		<td><?= $row['idbarang'].' - '.$row['namabarang'] ?></td>
		<td><?= $row['merk'] ?></td>
		<td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
		<? if(empty($r_pegawai)) {?>
		<td><?= $row['namalengkap'] ?></td>		
		<? } ?>
		<td align="center"><?= $row['kondisi'] ?></td>
		<td align="center"><?= $row['status'] ?></td>
	</tr>
    <?php
        }
        if($i == 0){
    ?>
	<tr>
		<td colspan="7" align="center">-- Data tidak ditemukan --</td>
    </tr>
    <?php
        }
    ?>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td width="35%">Jakarta, &nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?><?//= str_repeat('.',40) ?></td>
		<td width="40%">&nbsp;</td>
		<? if ($a_pegawai['pegawai']) { ?>
		<td>&nbsp;</td>
		<? } ?>
	</tr>
	<tr>
		<? if ($a_pegawai['pegawai']) { ?>
		<td>Pemakai</td>
		<? } ?>
		<td>Penanggung Jawab Unit</td>
		<td>Pengelola Aset</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr valign="top">
		<? if ($a_pegawai['pegawai']) { ?>
		<td><?= $a_pegawai['pegawai'] ?></td>
        <? } ?>
		<td><?= $a_petugas['pegawai'] ?></td>
		<td>Ka. Bag. RT</td>
	</tr>
</table>
</div>
</body>
</html>
