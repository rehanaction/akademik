<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_jurusan = CStr::removeSpecial($_REQUEST['jurusan']);
	$r_programPendidikan = CStr::removeSpecial($_REQUEST['progpend']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	
    // definisi variable halaman
	$p_title = '.:Profil Lulusan Berdasarkan Periode Wisuda Per Prodi:.';
	
	$p_tbwidth = 800;
	$p_namafile = 'ipk_lulusan_prodi'.$r_kodeunit;
	
	$rs = mLaporan::getIPKlulusprodi($conn,$r_jurusan,$r_programPendidikan);
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<head>
<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		.tab_header { border-bottom: 1px solid black; margin-bottom: 5px }
		.div_headeritem { float: left }
		.div_preheader, .div_header { font-family: "Times New Roman" }
		.div_preheader { font-size: 15px; font-weight: bold }
		.div_header { font-size: 15px }
		.div_headertext { font-size: 12px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head { border-bottom: 1px solid black }
		.tb_head td { font-size: 10px }
		.tb_head .mark { font-size: 11px }
		.div_head { font-size: 16px; text-decoration: underline }
		.div_subhead { font-size: 14px; margin-bottom: 5px }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; padding: 1px }
		.tb_data th { background-color: #CFC; font-size: 11px }
		.tb_data td { font-size: 20px }
		.tb_data .noborder th { border-left: none; border-right: none }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 15px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body>
<div align="center">
<?php
	
		include('inc_headerlap.php');
?>
<div class="div_head">PROFIL LULUSAN BERDASARKAN PERIODE WISUDA PER PRODI</div>
</br>
 <table  border ="0" width="<?= $p_tbwidth ?>">
<tr>
    <tr valign ="top">
     <td width="75">Prodi</td>
     <td align="center" width="10">:</td>
	 <td width="310" class="mark"><?= mUnit::getNamaUnit($conn,$r_jurusan) ?></td>
	 </tr>
	 <tr valign ="top">
     <td>Program Pendidikan</td>
     <td align="center" width="10">:</td>
	 <td width="310" class="mark"><?= $r_programPendidikan?></td>
	 </tr>
   </table>
   <br>
<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
<tr bgcolor ="green">
    <th rowspan="2" style="color:#FFFFFF">Tanggal Lulus</th>
	<th rowspan="2" style="color:#FFFFFF">Tanggal yudisium</th>
	<th rowspan="2" style="color:#FFFFFF">Periode Lulus</th>
	<th colspan="2" style="color:#FFFFFF">IPK <2.5</th>
	<th colspan="2" style="color:#FFFFFF">IPK 2.5-3.0</th>
	<th colspan="2" style="color:#FFFFFF">IPK >3.0</th>
	<th rowspan="2" style="color:#FFFFFF">Total Lulusan</th>
	<th rowspan="2" style="color:#FFFFFF">IPK Rata- rata </th>
	</tr>
	<tr bgcolor ="green">
	<th style="color:#FFFFFF">Jumlah</th>
	<th style="color:#FFFFFF">%</th>
	<th style="color:#FFFFFF">Jumlah</th>
	<th style="color:#FFFFFF">%</th>
	<th style="color:#FFFFFF">Jumlah</th>
	<th style="color:#FFFFFF">%</th>
</tr>
<? 
	$i = 0;
	while ($row = $rs->FetchRow ()){ 
	$i++; ?>
	<tr>
	<td align ="center"><?=$row['idlulus']?></td>
	<td><?=CStr::formatDateInd ($row['tgllulus'])?></td>
	<td><?=$row['periodelulus']?></td>
	<td><?=$row['ipk1']?></td>
	<td><?=$row['persenipk1']?></td>
	<td><?= $row['ipk2']?></td>
	<td><?= $row['persenipk2']?></td>
	<td><?=$row['ipk3']?></td>
	<td><?= $row['persenipk3']?></td>
	<td><?= $row['total']?></td>
	<td><?=$row['rataipklulus']?></td>
	</tr>
	<? }?>
	
	</table>
	<table>
	<tr>
		<td width="650">&nbsp;</td>
		<td class="mark"><?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr></table>
 </body></html>