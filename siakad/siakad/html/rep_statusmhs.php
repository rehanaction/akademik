<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug= true;
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_jurusan = "4302160000";
	//$r_angkatan1 = CStr::removeSpecial($_REQUEST['angkatan1']);
	//$r_angkatan2 = CStr::removeSpecial($_REQUEST['angkatan2']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	// definisi variable halaman
	$p_title = 'Rekapitulasi Mahasiswa Berdasarkan Status Akademik';
	
	$p_tbwidth = 900;
	$p_namafile = 'status_mhs_'.$r_kodeunit;
	
     $rs = mLaporan::getStatusmhs($conn,$r_jurusan,$r_semester,$r_tahun);
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
<div class="div_head">REKAPITULASI MAHASISWA BERDASARKAN STATUS AKADEMIK</div>
</br>
 <table  border ="0" width="<?= $p_tbwidth ?>">
  <tr valign ="top">
     <td width="75">Prodi</td>
     <td align="center" width="10">:</td>
	 <td width="310" class="mark"><?= mUnit::getNamaUnit($conn,$r_jurusan) ?></td>
	 </tr>
	 <tr valign ="top">
	 <td>Periode Semester</td>
     <td align="center" width="10">:</td>	
     <td>  
	 <? 
	if ($r_semester == "1")
	{
	echo "Ganjil";
	}
	elseif($r_semester == "2")
	{
	echo "Genap";
	}
	elseif($r_semester == "3")
	{
	echo "Pendek";
	}?>
	 /<?= $r_tahun; ?></td>
   </tr></table>
<br>
<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="1">
<tr bgcolor = "green">
	<th rowspan="2" style = "color:#FFFFFF">No</th>
	<th rowspan="2" style = "color:#FFFFFF">Unit</th>
	<th rowspan="2" style = "color:#FFFFFF">Angkatan</th>
	<th colspan="7" style = "color:#FFFFFF">Status Akademik Mahasiswa</th>
	<th rowspan="2" style = "color:#FFFFFF">Jmh MHS</th>
	</tr>
	<tr bgcolor ="green">
	<th style = "color:#FFFFFF">Aktif</th>
	<th style = "color:#FFFFFF">Cuti</th>
	<th style = "color:#FFFFFF">Lulus</th>
	<th style = "color:#FFFFFF">DO</th>
	<th style = "color:#FFFFFF">Pasif</th>
	<th style = "color:#FFFFFF">Mengundurkan Diri</th>
	<th style = "color:#FFFFFF">Wafat</th>
</tr>
<? 
$i=0;
while ($row = $rs->FetchRow ()){ 
$i++;
?>
	<tr>
	<td><?=$i;?></td>
	<td><?=$row['namaunit']?></td>
	<td><?=$row['angkatan']?></td>
	<td><?=$row['stsaktif']?></td>
	<td><?= $row['stscuti']?></td>
	<td><?= $row['stslulus']?></td>
	<td><?= $row['stsdo']?></td>
	<td><?=$row['stspasif']?></td>
	<td><?=$row['stsundur']?></td>
	<td><?=$row['stswafat']?></td>
	<td><?=$row['total']?></td>
	</tr>
	<? }?>
	
	</table>
	<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td width="750">&nbsp;</td>
		<td class="mark"><?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
	</table>
 </body></html>