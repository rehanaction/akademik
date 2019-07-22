<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	$conn_sdm->debug=$conn->debug;
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_bulanbayar = CStr::removeSpecial($_REQUEST['bulanbayar']);
	$r_tahunbayar = CStr::removeSpecial($_REQUEST['tahunbayar']);
	$r_nip = CStr::removeSpecial($_REQUEST['nipdosen']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$r_periode=$r_tahun.$r_semester;
	$r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	
	if(Akademik::isDosen())
		$r_nip = Modul::getUserName();
		
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('honordosen'));
	require_once(Route::getModelPath('jeniskuliah'));
	
	require_once(Route::getMOdelPath('sistemkuliah'));
	//$conn_sdm->debug=true;
	$namaunit=mUnit::getNamaUnit($conn,$r_kodeunit);
	//$fakultas=mUnit::getNamaParentUnit($conn,$r_jurusan);
	$jeniskuliah=mJeniskuliah::getArray($conn);
	// properti halaman
	$p_title = 'Laporan Rekapitulasi Pengajaran Dosen';
	$p_tbwidth = 800;
	$p_namafile = 'rekap_ajar'.$r_kodeunit;
	
	$a_sistemkuliah=mSistemkuliah::getArray($conn);
	
	$a_data = mLaporan::getRekapMengajar($conn,$r_kodeunit,$r_periode,$r_periodegaji,'','',$r_nip);
	
	$status_kuliah=array('0'=>'Tatap Muka','-1'=>'Online');	
	$statusajar=array('0'=>'Dibayarkan','-1'=>'Kewajiban');
	 $arr_data=array();
	 foreach($a_data as $row){
		if(($row['tglkuliah']!=$row['tglkuliahrealisasi']) or ($row['waktumulai']!=$row['waktumulairealisasi']) or ($row['waktuselesai']!=$row['waktuselesairealisasi']))
			$status='Make Up';
		else
			$status='Per jadwal';
		
		//setting sks
		if(empty($row['skshonor'])){
			$sks=$row['sks'];
			$skstatapmuka=$row['skstatapmuka'];
			$skspraktikum=$row['skspraktikum'];
			if(empty($skstatapmuka) and empty($skspraktikum))
				$skstatapmuka=$sks;
			else if(empty($skstatapmuka) and !empty($skspraktikum))
				$skstatapmuka=$sks-$skspraktikum;
			else if(!empty($skstatapmuka) and empty($skspraktikum))
				$skspraktikum=$sks-$skstatapmuka;
				
			if(strtoupper($row['jeniskuliah'])=='P')
				$skshonor=$skspraktikum;
			else
				$skshonor=$skstatapmuka;
		}else
			$skshonor=$row['skshonor'];
					
		$arr_data[]=array(
													'kodemk'=>$row['kodemk'],
													'namamk'=>$row['namamk'],
													'kelasmk'=>$row['kelasmk'],
													'perkuliahanke'=>$row['perkuliahanke'],
													'sesi'=>$row['kelasmk'],
													'sks'=>$skshonor,
													'tglkuliah'=>Date::indoDate($row['tglkuliahrealisasi'],false),
													'basis'=>$row['basis'],
													'isonline'=>$row['isonline'],
													'perkuliahanke'=>$row['perkuliahanke'],
													'status'=>$status,
													'keterangan'=>$statusajar[$row['tugasmengajar']],
													'tugasmengajar'=>$row['tugasmengajar'],
													'jeniskuliah'=>$jeniskuliah[$row['jeniskuliah']],
													'honor'=>$row['honordosen'],
													'nopengajuan'=>$row['nopengajuan'],
													'keterangan'=>$row['keterangan']);
	 }
	
	$a_datapegawai=mPegawai::getTarifDosen($conn_sdm,$r_nip);
	$xyx=array();
	foreach($a_datapegawai as $kode=>$rate){ 
		$norekening=$rate['norekeninghonor'];
		$anrekeninghonor=$rate['anrekeninghonor'];
		
		$namajnsrate=$rate['namajnsrate'];
		if ( $kode=="01" && $xyx[$kode] ) continue;
		if ( $kode=="02" && !empty($rate['nominal']) ){ $kode="01"; $namajnsrate="Reguler"; }
		if ( $kode=="08" && $xyx[$kode] ) continue;
		if ( $kode=="09" && !empty($rate['nominal']) ){ $kode="08"; $namajnsrate="Pasca Sarjana"; }

		$xyx[$kode]=array( 'norekeninghonor'=>$rate['norekeninghonor'],
				'namajnsrate'=>$namajnsrate,
				'nominal'=>$rate['nominal']
				);
	}
	
	// header
	ksort($arr_data);
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
<div align="left" style="width:<?= $p_tbwidth ?>px"><b>Detil Pembayaran Honor Mengajar</b></div>

<br>
<table width="<?= $p_tbwidth ?>" class="tb_head">
	<tr>
		<td>Periode Semester</td>
		<td>:</td>
		<td><?=Akademik::getNamaPeriode($r_periode)?></td>
	</tr>
	<tr>
		<td>Dosen</td>
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
		<td><?=mHonorDOsen::convertPeriodeGaji($r_periodegaji)?></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<?php foreach($xyx as $koderate=>$rate){ ?>
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
    <th >Mata Kuliah</th>
	<th  >SKS</th>
	<th  >Sesi</th>
	<th  >Tanggal</th>
	<th >Aktifitas</th>
	<th >Status</th>
	<th >jenis</th>
	<th width="50" >Pertemuan</th>
	<th >Sub Total</th>
	<th >Keterangan</th>
	<th >No Pengajuan</th>
	<!--th >Keterangan</th-->
</tr>
</thead>  
<tbody>	
	<? 
	$i=0;
	$tot_honor=0;
	foreach($arr_data as $key=>$row){ ?>
	<tr>	
		<td class="marger"><?=$row['kodemk'].' '.$row['namamk']?></td>
		<td align="center"><?=$row['sks']?></td>
		<td align="center"><?=$row['sesi']?></td>
		<td><?=$row['tglkuliah']?></td>
		<td><?=$row['jeniskuliah']?></td>
		<td><?=$status_kuliah[$row['isonline']]?></td>
		<td><?=$row['status']?></td>
		<td align="center"><?=$row['perkuliahanke']?></td>
		<td align="right"><?=$row['tugasmengajar']=='0'?number_format($row['honor'],2,',','.'):'0'?></td>
		<td><?=$row['keterangan']?></td>
		<td><?=$row['nopengajuan']?></td>
	</tr>
	<?php
		if($row['tugasmengajar']=='0')
			$tot_honor+=$row['honor'];
		} 
	?>
	<tr>
		<td colspan="8">&nbsp;</td>
		<td align="right"><?=number_format($tot_honor,2,',','.')?></td>
		<td colspan="2">&nbsp;</td>
	</tr>
	
	</tbody>
	</table>
	
	</div>
 </body>
 </html>
