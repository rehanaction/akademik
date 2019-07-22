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
	$r_nip = CStr::removeSpecial($_REQUEST['nipasisten']);
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
	require_once(Route::getModelPath('honorasisten'));
	
	
	//$conn_sdm->debug=true;
	$namaunit=mUnit::getNamaUnit($conn,$r_kodeunit);
	
	// properti halaman
	$p_title = 'Laporan Detail Honor Asisten';
	$p_tbwidth = 650;
	$p_namafile = 'rekap_ajar'.$r_kodeunit;
	
	$a_sistemkuliah=mSistemkuliah::getArray($conn);
	$status_kuliah=array('0'=>'Tatap Muka','-1'=>'Online');	
	$a_statuskuliah=array('S'=>'Per Jadwal','M'=>'Make Up');
	$a_rate=mRateHonor::getJenisRate($conn,'PA');
	
	$a_data = mLaporanHonor::getHonorAsisten($conn,$r_kodeunit,$r_periode,$r_periodegaji,$r_nopengajuan,$r_sistemkuliah,$r_nip);
	
	$a_datapegawai=mPegawai::getTarifDosen($conn_sdm,$r_nip);
	
	
	foreach($a_datapegawai as $koderate=>$rate){ 
		$norekening=$rate['norekeninghonor'];
		$anrekeninghonor=$rate['anrekeninghonor'];
	}
	
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
foreach($a_data as $nip=>$arr_gaji){ 
	$rowp=$arr_gaji[0];
	?>
<?php
	
		include('inc_headerlap.php');
?>
<div align="left" style="width:<?= $p_tbwidth ?>px"><b><?=$p_title?></b></div>

<br>
<table width="<?= $p_tbwidth ?>" class="tb_head">
	<tr>
		<td>Periode Semester</td>
		<td>:</td>
		<td><?=Akademik::getNamaPeriode($r_periode)?></td>
	</tr>
	<tr>
		<td>Asisten</td>
		<td>:</td>
		<td><?=$r_nip.'-'.mPegawai::getNamaPegawai($conn,$r_nip)?></td>
	</tr>
	<tr>
		<td>Nomor Account Bank</td>
		<td>:</td>
		<td><?=$norekening?></td>
	</tr>
	<tr>
		<td>Nama Account Bank</td>
		<td>:</td>
		<td><?=$anrekeninghonor?></td>
	</tr>
	<tr>
		<td>Periode Honor</td>
		<td>:</td>
		<td><?=mHonorAsisten::convertPeriodeGaji($r_periodegaji)?></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<?php foreach($a_datapegawai as $koderate=>$rate){ ?>
	<tr>
		<td>Rate <?=$rate['namajnsrate']?></td>
		<td>:</td>
		<td><?=number_format($rate['nominal'],2,',','.')?></td>
	</tr>
	<?php } ?>
</table>


<table width="<?= $p_tbwidth ?>" class="tb_data" id="myTable">
<thead>
<tr bgcolor= 'green'>
    <tr bgcolor= 'green'>
    <th >Mata Kuliah</th>
	<th  >SKS</th>
	<th  >Sesi</th>
	<th  >Tanggal</th>
	<th >Aktifitas</th>
	<th >Status</th>
	<th >jenis</th>
	<th width="50" >Pertemuan Ke</th>
	<th >Sub Total</th>
	<th >Keterangan</th>
</tr>
	
</tr>
</thead>  
<tbody>	
	<? 
	$i=0;
	$tot_honor=0;
	foreach($arr_gaji as $key=>$row){ ?>
	<tr>	
		<td class="marger"><?=$row['kodemk'].' '.$row['namamk']?></td>
		<td align="center"><?=$row['sks']?></td>
		<td align="center"><?=$row['kelasmk']?></td>
		<td><?=CStr::formatDateInd($row['tglkuliahrealisasi'])?></td>
		<td><?=$row['jeniskuliah']?></td>
		<td><?=$status_kuliah[$row['isonline']]?></td>
		<td><?=$a_statuskuliah[$row['statusperkuliahan']]?></td>
		<td align="center"><?=$row['perkuliahanke']?></td>
		<td align="right"><?=number_format($row['honor'],2,',','.')?></td>
		<td>Dibayarkan</td>
	</tr>
	<?php
		
			$tot_honor+=$row['honor'];
		} 
	?>
	<tr>
		<td colspan="8">&nbsp;</td>
		<td align="right"><?=number_format($tot_honor,2,',','.')?></td>
		<td>&nbsp;</td>
	</tr>
	
	</tbody>
	</table>
	<br>
<?php } ?>
	</div>
 </body></html>
