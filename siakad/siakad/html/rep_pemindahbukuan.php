<?php

	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=false;
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_bulanbayar = CStr::removeSpecial($_REQUEST['bulanbayar']);
	$r_tahunbayar = CStr::removeSpecial($_REQUEST['tahunbayar']);
	$r_nopembayaran = CStr::removeSpecial($_REQUEST['nopembayaran']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$r_periode=$r_tahun.$r_semester;
	$r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getMOdelPath('sistemkuliah'));
	require_once(Route::getMOdelPath('honordosen'));
	
	
	// properti halaman
	$p_title = 'REKAPITULASI TRANSFER HONOR MENGAJAR';
	$p_tbwidth = 700;
	$p_namafile = 'rekap_ajar'.$r_kodeunit;
	
	$a_sistemkuliah=mSistemkuliah::getTipe($conn);
	$a_data = mLaporan::getPemindahBukuan($conn,$r_kodeunit,$r_periode,$r_periodegaji,$r_nopembayaran);
	
	//buat array nipdosen
	$nipdosen=array();
	foreach($a_data['data'] as $key=>$datadosen)
		$nipdosen[]=$datadosen['nipdosenrealisasi'];
	
	//ambil data dari sdm
	$arr_pegawai=mPegawai::getDataDosen($conn_sdm,$nipdosen);
		
	$arr_data=array();
	foreach($a_data['data'] as $key=>$data){
		$nip=$data['nipdosenrealisasi'];
		$pros_pajak=$arr_pegawai[$nip]['pph'];
		$pajak=($pros_pajak/100)*$data['jum_honor'];
		$pajak=round($pajak);
		$honor=$data['jum_honor']-$pajak;
	
		$arr_data[$nip]['namadosen']=$data['namadosen'];
		$arr_data[$nip]['norekeninghonor']=$arr_pegawai[$nip]['norekeninghonor'];
		$arr_data[$nip]['anrekeninghonor']=$arr_pegawai[$nip]['anrekeninghonor'];
		$arr_data[$nip]['gaji'][]=$honor;
		//break;
	}
	$a_honor=array();
	 foreach($arr_data as $nip=>$row){
		 $a_honor[$nip]=array('namadosen'=>$row['namadosen'],'norekeninghonor'=>$row['norekeninghonor'],'anrekeninghonor'=>$row['anrekeninghonor'],'honor'=>array_sum($row['gaji']));
	 }
	 
	foreach($a_data['inv'] as $key=>$inv)
		$head['nopengajuan']=$inv['nopengajuan'];
	
	ksort($a_honor);
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
		<td width="100">Kode Pembayaran</td>
		<td width="10">:</td>
		<td><?=$r_nopembayaran?></td>
	</tr>
	<tr>
		<td>Periode Pembayaran</td>
		<td>:</td>
		<td><?=mHonorDOsen::convertPeriodeBayar($head['nopengajuan'])?></td>
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
	<td><?=strtoupper($a_sistemkuliah[trim($invoice['sistemkuliah'])])?></td>
	</tr>
	<? }?>
</table>
<br>
<table width="<?= $p_tbwidth?>" class="tb_data">
	<thead>
		<tr>
			<th align="center">REKENING</th>
			<th align="center">PLUS</th>
			<th align="center">NOMINAL</th>
			<th align="center">CD</th>
			<th align="center">NO</th>
			<th align="center">NAMA</th>
			<th align="center">KETERANGAN</th>
			<th align="center">REKENING PERUSAHAAN</th>
		</tr>
	</thead>
	<tbody>
	<? 
	$no=0;
	$tot_honor=0;
	$biaya_trasnfer=0;
	foreach($a_honor as $nip=>$data){
		$no++;
		//honor dikurangi bia transfer
		$biatrans=round($arr_pegawai[$nip]['biatrans']);
		$honor=$data['honor']-$biatrans;
		$tot_honor+=$honor;
		$biaya_trasnfer+=$biatrans;
		
	?>
	<tr>
		<td align="center"><?=$data['norekeninghonor']?></td>
		<td align="center">+</td>
		<td align="right"><?= number_format($honor,2,',','.') ?></td>
		<td align="center">C</td>
		<td align="right"><?= str_pad($no, 4, "0", STR_PAD_LEFT); ?></td>
		<td align="left"><?=!empty($data['anrekeninghonor'])?strtoupper($data['anrekeninghonor']):$data['namadosen'].' belum mempunyai rekening'?></td>
		<td align="left">HONOR MENGAJAR</td>
		<td align="center">1650008886666</td>
	</tr>
	<? }
	$no++;
	?>
	<tr>
		<td align="center">1650000497439</td>
		<td align="center">+</td>
		<td align="right"><?= number_format($biaya_trasnfer,2,',','.'); ?></td>
		<td align="center">C</td>
		<td align="right"><?= str_pad($no++, 4, "0", STR_PAD_LEFT); ?></td>
		<td>YAY PEND KEMALA BGS</td>
		<td>BIAYA PAYROLL</td>
		<td align="center">1650008886666</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="right"><?= number_format($tot_honor,2,',','.'); ?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
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
