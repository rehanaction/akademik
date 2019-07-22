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
	$r_tahunbayar = CStr::removeSpecial($_REQUEST['tahunbayar']);
	$r_bulanbayar = CStr::removeSpecial($_REQUEST['bulanbayar']);
	$r_nopengajuan = CStr::removeSpecial($_REQUEST['nopengajuan']);
	$r_jenispenguji = CStr::removeSpecial($_REQUEST['jenispenguji']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$r_periode=$r_tahun.$r_semester;
	$r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	
	require_once(Route::getModelPath('laporanhonor'));
	require_once(Route::getModelPath('honorpenguji'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('combo'));
	
	
	
	// properti halaman
	$p_title = 'Laporan Rekapitulasi Honor Penguji';
	$p_tbwidth = 700;
	$p_namafile = 'rekap_ajar'.$r_periode;
	
	$a_jenispenguji=mHonorPenguji::getJenisSidang();
	
	$a_data = mLaporanHonor::getRekapHonorPenguji($conn,$r_kodeunit,$r_periode,$r_periodegaji,$r_nopengajuan,$r_jenispenguji);
	
	
	 $a_nodosen=array();
	 foreach($a_data as $row)
		 $a_nodosen[]=$row['nipdosen'];
		
	//ambil data dari SD
	$arr_pegawai=mPegawai::getDataDosen($conn_sdm,$a_nodosen);
	
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
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }
		.tb_foot_ttd td { padding-right:80px }
		
		
		
		
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
		<td width="100">Penyelenggara</td>
		<td>:</td>
		<td><?=mUnit::getNamaUnit($conn,$r_kodeunit)?></td>
	</tr>
	<tr>
		<td>Periode</td>
		<td>:</td>
		<td><?=Akademik::convertPeriodeGaji($r_periodegaji)?></td>
	</tr>
	<tr>
		<td>No Bukti</td>
		<td>:</td>
		<td><?=$r_nopengajuan?></td>
	</tr>
	<tr>
		<td>Semester</td>
		<td>:</td>
		<td><?=Akademik::getNamaPeriode($r_periode)?></td>
	</tr>
	<tr>
		<td>Jenis Penguji</td>
		<td>:</td>
		<td><?=$a_jenispenguji[$r_jenispenguji]?></td>
	</tr>
</table>

	

<br>
<table width="<?= $p_tbwidth ?>" class="tb_data">
<thead>
<tr bgcolor= 'green'>
   
    <th width="80" >Kode</th>
	<th  >Nama Dosen</th>
	<th width="100" >Honor</th>
	<th width="100" >Pajak%</th>
	<th width="100" >PPh21</th>
	<th width="100" >Honor stl Pjk</th>
	
	
</tr>
</thead>  
<tbody>	
	<? 
	$i=0;
	$total_honor=0;
	$total_honorp=0;
	$total_pajak=0;
	foreach($a_data as $row_honor){
	$nip=$row_honor['nipdosen'];
	$i++;
	$pajak=0;
	
	$pros_pajak=$arr_pegawai[$nip]['pph'];
	$pajak=($pros_pajak/100)*$row_honor['jumlahhonor'];
	$pajak=round($pajak);
	$honor=$row_honor['jumlahhonor']-$pajak;
	
	$total_honor+=$row_honor['jumlahhonor'];
	$total_honorp+=$honor;
	$total_pajak+=$pajak;
	?>
	<tr>
	
	<td><?=$row_honor['nipdosen']?></td>
	<td><?=$row_honor['namadosen']?></td>
	<td align="right"><?=number_format($row_honor['jumlahhonor'],0,',','.')?></td>
	<td align="right"><?=number_format($pros_pajak,2,',','.')?></td>
	<td align="right"><?=number_format($pajak,0,',','.')?></td>
	<td align="right"><?=number_format($honor,0,',','.')?></td>
	</tr>
	<? }?>
	<tr>
	<td align="center" colspan="2"><b>Total</b></td>
	<td align="right"><b><?=number_format($total_honor,0,',','.')?></b></td>
	<td><b>&nbsp;</b></td>
	<td align="right"><b><?=number_format($total_pajak,0,',','.')?></b></td>
	<td align="right"><b><?=number_format($total_honorp,0,',','.')?></b></td>
	</tr>
	</tbody>
	</table>
	<br>
	<div id="foot">
		<table class="tb_foot tb_foot_ttd" width="<?= $p_tbwidth ?>">
			<tr>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>">Jakarta, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
			</tr>
			<tr>
				<td colspan="3" height="50">&nbsp;</td>
			</tr>
			<tr>
				<td>Adi Widianto, S.Kom</td>
				<td>Siwi Aryantini, SE</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><hr></td>
				<td><hr></td>
				<td><hr></td>
			</tr>
			<tr>
				<td>Wakil Rektor II</td>
				<td>Ka Dept Keuangan</td>
				<td>Wakil Dekan/Ka prodi</td>
			</tr>
		</table>
		<br>
		<table class="tb_foot" width="<?= $p_tbwidth ?>">
			<tr>
				<td colspan="3"><b>Keterangan</b> <br> Untuk dicantumkan pada PAB adalah :</td>
			</tr>
			<tr>
				<td align="left" width="<?= $p_tbwidth-600 ?>">Honor yang dibayarkan</td>
				<td align="right" width="<?= $p_tbwidth-650 ?>"><?=number_format($total_honorp,2,',','.')?></td>
				<td width="<?= $p_tbwidth-200 ?>">&nbsp;</td>
			</tr>
			<tr>
				<td align="left">Jumlah PPh 21</td>
				<td align="right"><?=number_format($total_pajak,2,',','.')?></td>
				<td width="<?= $p_tbwidth-200 ?>">&nbsp;</td>
			</tr>
		</table>
	</div>
	
	</div>
 </body>
 </html>
