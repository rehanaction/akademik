<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporanmhs'));
	require_once(Route::getModelPath('unit'));
	//$conn->debug=false;
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	
	
	// $r_periode = Akademik::getPeriode();
	$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	
	// properti halaman
	$p_title = 'Laporan Rekap Nilai Mahasiswa';
	$p_tbwidth = 720;
	
	$a_data = mLaporanMhs::getNilaiMhs($conn,$r_periode,$r_kodeunit);
	
	// header
	Page::setHeaderFormat($r_format,'rekap Nilai Mahasiswa '.$r_kodeunit.'_'.$r_periode);
	//print_r($a_data);die();
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
		.div_preheader { font-size: 10px; font-weight: bold }
		.div_header { font-size: 12px }
		.div_headertext { font-size: 9px; font-style: italic }
		
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
		.tb_data td { font-size: 10px }
		.tb_data .noborder th { border-left: none; border-right: none }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 11px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body>
<div align="center">
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="10" align="center">Data Nilai Mahasiswa <?=mUnit::getNamaUnit($conn,$r_kodeunit)?> <?=Akademik::getNamaPeriode($r_periode)?></td>
	</tr>
	<tr>
		<td colspan="10">&nbsp;</td>
	</tr>
	<tr>
		<th >SEMESTER</th>
		<th >MATAKULIAH</th>
		<th >SEKSI</th>
		<th >NIM</th>
		<th >NAMA MAHASISWA</th>
		<th>NA</th>
		<th >NH</th>
	
	</tr>
<?php
$i=0;
	foreach ($a_data as $row){
		$i++;
?>
	<tr>
		<td align="center"><?=Akademik::getNamaPeriode($r_periode,true)?></td>
		<td align="center"><?=$row['kodemk']?> <?=$row['namamk']?></td>
		<td align="center"><?=$row['kelasmk']?></td>	
		<td align="center"><?=$row['nim']?></td>
		<td align="left"><?=$row['nama']?></td>
		<td align="center"><?=$row['nnumerik']?></td>
		<td align="center"><?=$row['nhuruf']?></td>
	</tr>
<?php } ?>
</table>


</div>
</body>
</html>
