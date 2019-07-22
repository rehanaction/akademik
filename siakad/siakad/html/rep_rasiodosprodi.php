<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_jurusan = CStr::removeSpecial($_REQUEST['jurusan']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	
	// properti halaman
	$p_title = 'Laporan Rasio Dosen Per-Prodi';
	$p_tbwidth = 800;
	$p_namafile = 'rasio_dosen_prodi'.$r_kodeunit;
	
	$rs = mLaporan::getRasiodosprodi($conn,$r_jurusan);
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
<div class="div_head">LAPORAN RASIO DOSEN PER PRODI</div>
</br>
 <table  border ="0" width="<?= $p_tbwidth ?>">
 <tr valign ="top">
     <td width="50">Prodi</td>
     <td align="center" width="10">:</td>
	 <td width="310" class="mark"><?= mUnit::getNamaUnit($conn,$r_jurusan) ?></td>
	 </tr></table>
<br>
<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
<tr bgcolor= "green">

    <th rowspan ="2" style="color:#FFFFFF">Unit</th>
	<th rowspan ="2" style="color:#FFFFFF">Periode</th>
	<th colspan ="3" style="color:#FFFFFF">Jumlah Dosen dan Mahasiswa</th>
	<th rowspan ="2" style="color:#FFFFFF">Rasio Dosen</th>
	</tr>
	<tr bgcolor ="green">
	<th style="color:#FFFFFF">Jumlah Dosen</th>
	<th style="color:#FFFFFF">Jumlah MHS</th>
	<th style="color:#FFFFFF">%</th>
	</tr>

	<? 
	while ($row = $rs->FetchRow ()){
	?>
	<tr>
	<td><?=$row['namaunit']?></td>
	<td><?=$row['periode']?></td>
	<td><?=$row['jmldosen']?></td>
	<td><?=$row['jmlmhs']?></td>
	<td><?=$row['prosendosenmhs']?></td>
	<td><?= $row['rasiodosenmhs']?></td>
	</tr>
	<? }?>
	</table>
	<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td width="650">&nbsp;</td>
		<td class="mark"><?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
    </table></div>
 </body></html>