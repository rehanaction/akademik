<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	$conn->debug = true;
	// include
	require_once(Route::getModelPath('laporan'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_format = $_REQUEST['format'];
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
	
	if(!empty($r_key))
	
	// properti halaman
	$p_title = 'Kartu Inventaris Ruang';
	$p_tbwidth = 800;
	$p_ncol = 7;
	$p_namafile = 'kartu_inventaris_ruang_'.$r_key;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idlokasi' => $r_lokasi, 'unit' => $a_unit);
	$a_lokasi = mLaporan::getDataLokasi($conn,$r_lokasi);
	//$rs = mLaporan::getKIR($conn,$a_param);
	
	$sql="select s.idseri, s.idbarang1,b.namabarang as namabarang, l.namalokasi as namalokasi, u.namaunit as namaunit, 
		s.noseri, s.idkondisi, s.idstatus, s.merk, 
		s.spesifikasi, s.tglperolehan, s.idlokasi, s.idunit, p.namalengkap, k.kondisi, st.status
		from aset.as_seri s
		left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1
		left join aset.ms_lokasi l on l.idlokasi = s.idlokasi
		left join aset.ms_unit u on u.idunit = s.idunit
		left join sdm.v_biodatapegawai p on s.idpegawai = p.idpegawai
		left join aset.ms_kondisi k on k.idkondisi = s.idkondisi
		left join aset.ms_status st on st.idstatus = s.idstatus
		where s.idlokasi = '$r_lokasi' and
		u.infoleft >= {$a_unit['infoleft']} and u.inforight <= {$a_unit['inforight']} order by s.idlokasi,s.idpegawai";
	
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
    Kartu Inventaris Ruang<br/>
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
	<?/*
	<tr valign="top">
		<td width="60">Lokasi</td>
		<td width="5">:</td>
		<td><?= $a_param['idlokasi'] ?> - <?= $a_lokasi['namalokasi'] ?></td>
	</tr>
	*/?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
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
    <?php

        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
    ?>
	<tr valign="top">
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
		<td width="35%">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>Penanggung Jawab Ruang</td>
		<td>Pengelola Aset</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><?= $row['namalengkap'] ?></td>
		<td>Kabag. RT</td>
	</tr>
</table>
</div>
</body>
</html>
