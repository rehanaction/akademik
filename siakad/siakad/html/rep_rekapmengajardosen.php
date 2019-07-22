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
	$r_sistemkuliah = CStr::removeSpecial($_REQUEST['sistemkuliah']);
	$r_nopengajuan = CStr::removeSpecial($_REQUEST['nopengajuan']);
	$r_nip = CStr::removeSpecial($_REQUEST['nipdosen']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$r_periode=$r_tahun.$r_semester;
	$r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	
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
	
	$a_data = mLaporan::getRekapMengajar($conn,$r_kodeunit,$r_periode,$r_periodegaji,$r_sistemkuliah,$r_nopengajuan,$r_nip);
	
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
					
		$arr_data[$row['nipdosenrealisasi']][]=array(
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
													'keterangan'=>$row['keterangan']);
	 }
	
//print_r($arr_data);
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
<div align="left" style="width:<?= $p_tbwidth ?>px"><b>Rekapitulasi Kehadiran Dosen Detail</b></div>

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
		<td><?=mHonorDOsen::convertPeriodeGaji($r_periodegaji)?></td>
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
foreach($arr_data as $nip=>$arr_gaji){ 
	$arr_pegawai=mPegawai::getTarifDosen($conn_sdm,$nip);
	$xyx=array();
	foreach($arr_pegawai as $kode=>$rate){
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
	?>
<table width="<?= $p_tbwidth ?>" class="tb_head" >
	<tr>
		<td width="130"><?=$nip?></td>
		<td width="400"><?=mPegawai::getNamaPegawai($conn,$nip)?></td>
		<td width="310" rowspan="2" align="right">
			<table >
				<?php 
				$norekening='';
				foreach($xyx as $rate){ 
					$norekening=$rate['norekeninghonor'];
				?>
				<tr>
					<td>Rate <?=$rate['namajnsrate']?></td>
					<td><?=number_format($rate['nominal'],2,',','.')?></td>
				</tr>
				<?php } ?>
			</table>
			
		</td>
	</tr>
	<tr>
		<td>Nomor Account Bank</td>
		<td><?=$norekening?></td>
	</tr>
	<tr>
		
	</tr>
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
	<th width="50" >Pertemuan Ke</th>
	<th >Sub Total</th>
	<th >Keterangan</th>
	<!--th >Keterangan</th-->
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
		<td align="center"><?=$row['sesi']?></td>
		<td><?=$row['tglkuliah']?></td>
		<td><?=$row['jeniskuliah']?></td>
		<td><?=$status_kuliah[$row['isonline']]?></td>
		<td><?=$row['status']?></td>
		<td align="center"><?=$row['perkuliahanke']?></td>
		<td align="right"><?=$row['tugasmengajar']=='0'?number_format($row['honor'],2,',','.'):'0'?></td>
		<td><?=$row['keterangan']?></td>
		<!--td><?=$row['rumus']?></td-->
	</tr>
	<?php
		if($row['tugasmengajar']=='0')
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
<script>
$(function() { 
//Created By: Brij Mohan
//Website: http://techbrij.com
function groupTable($rows, startIndex, total){
	if (total === 0){
		return;
	}
		var i , currentIndex = startIndex, count=1, lst=[];
		var tds = $rows.find('td:eq('+ currentIndex +')');
		var ctrl = $(tds[0]);
		lst.push($rows[0]);
		for (i=1;i<=tds.length;i++){
		if (ctrl.text() ==  $(tds[i]).text()){
		count++;
		$(tds[i]).addClass('deleted');
		lst.push($rows[i]);
		}
		else{
			if (count>1){
				ctrl.attr('rowspan',count);
				groupTable($(lst),startIndex+1,total-1)
			}
			count=1;
			lst = [];
			ctrl=$(tds[i]);
			lst.push($rows[i]);
		}
	}
}
groupTable($("[id='myTable'] tr:has(td)"),0,1);
$("[id='myTable'] .deleted").remove();
});
</script>
