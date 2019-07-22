<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// Modul::getFileAuth();
	$r_kodeunit = CStr::removeSpecial($_GET['kodeunit']);
	$r_semester = CStr::removeSpecial($_GET['semester']);
	$r_tahun = CStr::removeSpecial($_GET['tahun']);
	
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	
	// properti halaman
	$p_title = 'Detail Mahasiswa Yang Berhak Mengikuti Ujian Skripsi';
	$p_tbwidth = 600;
	
	$rs = mLaporan::getDaftarMhsBerhakSidang($conn,$r_kodeunit,$r_semester,$r_tahun);
	
?>
<html>

<head>
<title>Detail Mahasiswa yang Berhak Mengikuti Ujian Skripsi</title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<style>
table { border-collapse:collapse }
div,td,th {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
}
td,th { border:1px solic black }
</style>
</head>
<body>
<div align="center">
<strong><?= $p_title ?></strong>
<br><br>
<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
<tr bgcolor= "green">
    <th style="color:#FFFFFF">No</th>
	<th style="color:#FFFFFF">NIM</th>
	<th style="color:#FFFFFF">Nama</th>
	<th style="color:#FFFFFF">Judul TA</th>
	
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	$i++;
	?>

	<tr>
	<td><?=$i;?></td>
	<td><?=$row['nim']?></td>
	<td><?=$row['nama']?></td>
	<td><?=$row['judulta']?></td>
	</tr>
	<? }?>
	</table></div>
 </body></html>