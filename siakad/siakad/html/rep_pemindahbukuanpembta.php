<?php

	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=false;
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_tahunbayar = CStr::removeSpecial($_REQUEST['tahunbayar']);
	$r_bulanbayar = CStr::removeSpecial($_REQUEST['bulanbayar']);
	$r_nopembayaran = CStr::removeSpecial($_REQUEST['nopembayaran']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$r_periode=$r_tahun.$r_semester;
	$r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	
	require_once(Route::getModelPath('laporanhonor'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('combo'));
	
	
	// properti halaman
	$p_title = 'Laporan Pemindah Bukuan Honor Pembimbing Tugas Akhir';
	$p_tbwidth = 700;
	$p_namafile = 'rekap_ajar'.$r_kodeunit;
	
	
	$a_data = mLaporanHonor::getPemindahbukuanPembTa($conn,$r_periodegaji,$r_nopembayaran,$r_unit);
	
	//buat array nipdosen
	$a_nipdosen=array();
	foreach($a_data['data'] as $key=>$datadosen)
		$a_nipdosen[]=$datadosen['nipdosen'];
	
	//ambil data dari sdm
	$arr_pegawai=mPegawai::getDataDosen($conn_sdm,$a_nipdosen);
		
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
		.no_border th, .no_border td {border:none}
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }
		
		
		
		
		
	</style>
</head>
<body>
<div align="center">
<?php
	
		include('inc_headerlap.php');
?>
<div align="left" style="width:<?= $p_tbwidth ?>px"><b><?=$p_title?></b></div>
<table width="<?= $p_tbwidth ?>" class="tb_head">
	<tr>
		<td width="100">Unit</td>
		<td width="10">:</td>
		<td><?=mUnit::getNamaUnit($conn,$r_unit)?></td>
	</tr>
	<tr>
		<td width="100">Kode Pembayaran</td>
		<td width="10">:</td>
		<td><?=$r_nopembayaran?></td>
	</tr>
	<tr>
		<td>Periode Pembayaran</td>
		<td>:</td>
		<td><?=Akademik::convertPeriodeGaji($r_periodegaji)?></td>
	</tr>
	<tr>
		<td>Pengajuan Invoice</td>
		<td>:</td>
		<td>&nbsp;</td>
	</tr>
</table>

<table width="<?= $p_tbwidth-200 ?>" class="tb_data no_border">
	<? 
	foreach($a_data['inv'] as $key=>$invoice){
	
	?>
	<tr>
	<td><?=$invoice['nopengajuan']?></td>
	<td><?=strtoupper($invoice['namaunit'])?></td>
	<td><?=$a_jenisujian[$invoice['jenisujian']]?></td>
	</tr>
	<? }?>
</table>
<br>
<table width="<?= $p_tbwidth?>" class="tb_data">
	<thead>
		<tr>
			<th align="center">No</th>
			<th align="center">Rekening</th>
			<th align="center">Nama</th>
			<th align="center">Nominal</th>
		</tr>
	</thead>
	<tbody>
	<? 
	$no=0;
	$tot_honor=0;
	$biaya_trasnfer=0;
	foreach($a_data['data'] as $data){
		$nip=$data['nipdosen'];
		$no++;
		
		//kurangi dengan PPH
		$pros_pajak=$arr_pegawai[$nip]['pph'];
		$pajak=($pros_pajak/100)*$data['jumlahhonor'];
		$pajak=round($pajak);
		$honor=$data['jumlahhonor']-$pajak;
	
		//honor dikurangi bia transfer
		$biatrans=round($arr_pegawai[$nip]['biatrans']);
		$honor=$honor-$biatrans;
		$tot_honor+=$honor;
		$biaya_trasnfer+=$biatrans;
		
	?>
	<tr>
		<td align="left"><?=$no?></td>
		<td align="left"><?=$arr_pegawai[$nip]['norekeninghonor']?></td>
		<td align="left"><?=!empty($arr_pegawai[$nip]['anrekeninghonor'])?strtoupper($arr_pegawai[$nip]['anrekeninghonor']):$data['namadosen'].' belum mempunyai rekening'?></td>
		<td align="right"><?=number_format($honor,0,',','.')?></td>
	</tr>
	<? }?>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="center"><b>Total Transfer honor Ajar</b></td>
		<td align="right"><?=number_format($tot_honor,0,',','.')?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>1650000497439</td>
		<td align="center"><b>Total Biaya Transfer</b></td>
		<td align="right"><?=number_format($biaya_trasnfer,0,',','.')?></td>
	</tr>
	</tbody>
</table>

	<br>
	<div id="foot">
		<table class="tb_foot" width="<?= $p_tbwidth ?>">
			
			
			<tr>
				<td>Jakarta</td>
				<td width="300">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" height="50">&nbsp;</td>
			</tr>
			<tr>
				<td><u>Dr. Ir. Arief Kusuma AP, MBA</u> <br> Rektor</td>
				<td>&nbsp;</td>
				<td>Dr. Suryanti T Arief, SH, M.Kn, MBA</td>
			</tr>
			
		</table>
	</div>
	
	</div>
 </body>
 </html>