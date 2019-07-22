<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	$r_periode=$r_tahun.$r_semester;
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	
//$conn->debug=true;
	// properti halaman
	$p_title = 'Laporan Mahasiswa Skripsi'.mUnit::getNamaUnit($conn,$r_unit);
	$p_tbwidth = "100%";
	$p_namafile = 'rasio_homebase_dosen'.$r_kodeunit;
	$level=mUnit::getLevelUnit($conn,$r_unit);
	$rs = mLaporan::getMahasiswaThesis($conn,$r_unit,$r_periode);
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
<div class="div_head">LAPORAN MAHASISWA SKRIPSI</div>
</br>
 <table  border ="0" width="<?= $p_tbwidth ?>">
 <tr valign ="top">
     <td width="50">Periode</td>
     <td align="center" width="10">:</td>
	 <td width="310" class="mark"><?=Akademik::getNamaPeriode($r_periode)?></td>
	 </tr>
<?php if($level!=0){ ?>
 <tr valign ="top">
     <td width="50"><?=$level==1?'Fakultas':'Prodi'?></td>
     <td align="center" width="10">:</td>
	 <td width="310" class="mark"><?= mUnit::getNamaUnit($conn,$r_unit) ?></td>
	</tr>
	
<?php } ?>
</table>
<br>
<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
<tr bgcolor= "green">
	<th style="color:#FFFFFF" width="10">No.</th>
    <th style="color:#FFFFFF" width="100">PRODI</th>
	<th style="color:#FFFFFF" width="100">NIM</th>
	<th style="color:#FFFFFF" width="100">NAMA MAHASISWA</th>
	<th style="color:#FFFFFF" width="100">MATAKULIAH</th>
	</tr>
	

	<? 
	$no=1;
	while ($row = $rs->FetchRow ()){ ?>
	<tr>
	<td align="center"><?=$no ?></td>
	<td align="center"><?=$row['namaunit']?></td>
	<td align="center"><?=$row['nim']?></td>
	<td><?=$row['nama']?></td>
	<td align="center">(<?=$row['kodemk']?>) <?=$row['namamk']?></td>
	</tr>

	<? $no++;} ?>
	
	</table>
	
    </div>
 </body>
 </html>
