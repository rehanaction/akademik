<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	//$conn_sdm->debug=true;
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_tahunbayar = CStr::removeSpecial($_REQUEST['tahunbayar']);
	$r_bulanbayar = CStr::removeSpecial($_REQUEST['bulanbayar']);
	$r_nopengajuan = CStr::removeSpecial($_REQUEST['nopengajuan']);
	$r_nip = CStr::removeSpecial($_REQUEST['nipdosen']);
	$r_sistemkuliah = CStr::removeSpecial($_REQUEST['sistemkuliah']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$r_periode=$r_tahun.$r_semester;
	$r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporanhonor'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('ratehonor'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('sistemkuliah'));
	
	
	//$conn_sdm->debug=true;
	$namaunit=mUnit::getNamaUnit($conn,$r_kodeunit);
	
	// properti halaman
	$p_title = 'Laporan Detail Honor Pembimbing Akademik';
	$p_tbwidth = 650;
	$p_namafile = 'rekap_ajar'.$r_kodeunit;
	
	$a_sistemkuliah=mSistemkuliah::getArray($conn);
	$a_rate=mRateHonor::getJenisRate($conn,'PA');
	
	$a_data = mLaporanHonor::getHonorDpa($conn,$r_kodeunit,$r_periode,$r_periodegaji,$r_nopengajuan,$r_sistemkuliah,$r_nip);
	
	Page::setHeaderFormat($r_format,$p_namafile);
	$conn->debug=false;
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
		.div_header { font-size: 12pt }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head td, .div_head { font-size: 12px }
		.div_subhead { font-size: 11px; margin-bottom: 5px }
		.div_head { text-decoration: underline }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 10px; padding: 2px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Tahoma, Arial }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }
		@media print
		{
			#foot{position:fixed;bottom:5px;}
		}
	</style>
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
</head>
<body>
<div align="center">
<?php
	
		include('inc_headerlap.php');
?>
<div align="left" style="width:<?= $p_tbwidth ?>px"><b><?=$p_title?></b></div>

<br>
<table width="<?= $p_tbwidth ?>" class="tb_head">
	<tr>
		<td width="100">Pengelola/Prodi</td>
		<td>:</td>
		<td><?=$namaunit?></td>
		<td width="100">No Pengajuan</td>
		<td>:</td>
		<td><?=$r_nopengajuan?></td>
	</tr>
	<tr>
		<td>Semester</td>
		<td>:</td>
		<td><?=Akademik::getNamaPeriode($r_periode)?></td>
		<td>Periode</td>
		<td>:</td>
		<td><?=Akademik::convertPeriodeGaji($r_periodegaji)?></td>
	</tr>
	<tr>
		<td>Basis</td>
		<td>:</td>
		<td><?=$a_sistemkuliah[$r_sistemkuliah]?></td>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="6">&nbsp;</td>
	</tr>
</table>
<?php 
foreach($a_data as $nip=>$arr_gaji){ 
	$arr_pegawai=mPegawai::getDataDosen($conn_sdm,$nip);
	?>
<table width="<?= $p_tbwidth ?>" class="tb_head">
	<tr>
		<td width="480">
			<table>
				<tr>
					<td><?=$nip?></td>
					<td><?=mPegawai::getNamaPegawai($conn,$nip)?></td>
				</tr>
				<tr>
					<td>Nomor Account Bank</td>
					<td><?=$arr_pegawai[$nip]['norekeninghonor']?></td>
				</tr>
			</table>
		</td>
		<td width="170">
			<table>
				<?php foreach($a_rate as $sistemkuliah=>$rate){ ?>
				<tr>
					<td>Rate <?=$sistemkuliah?></td>
					<td><?=number_format($rate,2,',','.')?></td>
				</tr>
				<?php } ?>
			</table>
		</td>
	</tr>
</table>
<table width="<?= $p_tbwidth ?>" class="tb_data" id="myTable">
<thead>
<tr bgcolor= 'green'>
    <th >NIM</th>
	<th  >Nama Mahasiwa</th>
	<th >Sub Total</th>
	
</tr>
</thead>  
<tbody>	
	<? 
	$i=0;
	$tot_honor=0;
	foreach($arr_gaji as $key=>$row){ ?>
	<tr>	
		<td class="marger"><?=$row['nim']?></td>
		<td align="left"><?=$row['nama']?></td>
		<td align="right"><?=number_format($row['honor'],2,',','.')?></td>
	</tr>
	<?php
		
			$tot_honor+=$row['honor'];
		} 
	?>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td align="right"><?=number_format($tot_honor,2,',','.')?></td>
	</tr>
	
	</tbody>
	</table>
	<br>
	<?php } ?>
	
	
	<div id="foot">
		<table class="tb_foot" width="<?= $p_tbwidth ?>">
			<tr>
				<td width="<?= $p_tbwidth-200 ?>"></td>
				<td>Mengetahui</td>
			</tr>
			<tr>
				<td colspan="3" height="50">&nbsp;</td>
			</tr>
			
			<tr>
				<td>&nbsp;</td>
				<td><hr></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Wakil Dekan/Ka prodi</td>
			</tr>
		</table>
	</div>
	</div>
 </body></html>
