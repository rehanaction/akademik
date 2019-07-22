<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporanmhs'));
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_angkatan = (int)$_REQUEST['angkatan'];
	$r_format = $_REQUEST['format'];
	
	if(Akademik::isMhs())
		$r_npm = Modul::getUserName();
	else
		$r_npm = CStr::removeSpecial($_REQUEST['npm']);
	
	// $r_periode = Akademik::getPeriode();
	$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	
	// properti halaman
	$p_title = 'Laporan KHS';
	$p_tbwidth = 720;
	
	if(empty($r_npm)) {
		$p_namafile = 'khs_'.$r_periode.'_'.$r_kodeunit.'_'.$r_angkatan;
		$a_data = mLaporanMhs::getKHSUnit($conn,$r_periode,$r_kodeunit,$r_angkatan);
	}
	else {
		$p_namafile = 'khs_'.$r_npm;
		$a_data = mLaporanMhs::getKHS($conn,$r_periode,$r_npm);
		
	}
	//echo count($a_data);
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
<body onload="window.print();">
<div align="center">
<?php
	$m = count($a_data);
	for($c=0;$c<$m;$c++) {
		$row = $a_data[$c];
		
		include('inc_headerlap.php');
?>
<div class="div_head">KARTU HASIL STUDI (KHS)</div>
<div class="div_subhead"><?= Akademik::getNamaPeriode($r_periode) ?></div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr valign="top">
		<td width="60"><strong>N I M</strong></td>
		<td align="center" width="10">:</td>
		<td width="310" class="mark"><?= $row['nim'] ?></td>
		<td width="60"><strong>Prodi</strong>
		<td align="center" width="10">:</td>
		<td><?= $row['namaunit'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Nama</strong></td>
		<td align="center">:</td>
		<td class="mark"><strong><?= $row['nama'] ?></strong></td>
		<td><strong>Semester</strong></td>
		<td align="center">:</td>
		<td><?= $row['semestermhs'] ?></td>
	</tr>
</table>
<div style="height:5px"></div>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="30" rowspan="2">No.</th>
		<th width="70" rowspan="2">Kode</th>
		<th rowspan="2">Nama Matakuliah</th>
		<th width="40" rowspan="2">KLS<br>MK</th>
		<th width="40" rowspan="2">SKS</th>
		<th colspan="2">Nilai (N)</th>
		<th width="50" rowspan="2">SKS x N</th>
	</tr>
	<tr>
		<th width="40">Huruf</th>
		<th width="40">Bobot</th>
	</tr>
<?php

		$t_tnsks = 0;
		$tsks=0;
		$n = count($row['khs']);
		for($i=0;$i<$n;$i++) {
			$rowk = $row['khs'][$i];
			
			//if(empty($rowk['nilaimasuk']) or empty($rowk['dipakai'])) {
			//	$t_nhuruf = '';
			//	$t_nangka = 0;
			//}
			//else {
				$cek = mLaporanMhs::cekInputanNilai($conn,$r_periode,$rowk['kodemk'],$row['nim']);
				$cek2 =  mLaporanMhs::cekInputanNilaiLengkap($conn,$r_periode,$rowk['kodemk'],$row['nim']);
			if($r_periode>=20181){	
				if($cek==0 and $cek2==3){
					$t_nhuruf = $rowk['nhuruf'];
					$t_nangka = $rowk['nangka'];
				}else{
					if($rowk['kodemk']=='LU25' or $rowk['kodemk']=='INA028' or $rowk['kodemk']=='INA029'){
						$t_nhuruf = $rowk['nhuruf'];
						$t_nangka = $rowk['nangka'];
					}else{
						$t_nhuruf='T';
						$t_nangka=0;
					}
				}
			}else{
				$t_nhuruf = $rowk['nhuruf'];
				$t_nangka = $rowk['nangka'];
			}

			//}
			$tsks = $tsks+$rowk['sks'];
			$t_nsks = $t_nangka*$rowk['sks'];
			$t_tnsks += $t_nsks;

			$tips = $t_tnsks / $tsks;
?>


	<tr>
		<td align="center"><?= $i+1 ?></td>
		<td align="center"><?= $rowk['kodemk'] ?></td>
		<td><?= $rowk['namamk'] ?></td>
		<td align="center"><?= $rowk['kelasmk'] ?></td>
		<td align="center"><?= $rowk['sks'] ?></td>
		<td align="center">
		<?= $t_nhuruf ?>
		
		
		
		
		</td>
		<td align="center"><?= $t_nangka ?></td>
		<td align="center"><?= $t_nsks ?></td>
		
	</tr>
<?php
		}

?>
	<tr  class="noborder">
		<th colspan="4">&nbsp;</th>
		<th><?= $tsks ?></th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th><?= $t_tnsks ?></th>
	</tr>
</table>
<div style="height:5px"></div>
<table class="tb_subfoot" width="<?= $p_tbwidth ?>">
	<tr>
		<td width="120">Indeks Prestasi Semester</td>
		<td align="center" width="10">=</td>
		<td width="250"><strong><?= round($tips, 2) ?></strong></td>
		<td width="130">Batas SKS Semester Depan</td>
		<td align="center" width="10">=</td>
		<td><?= $row['batassks'] ?></td>
	</tr>
	<tr>
		<td>Indeks Prestasi Kumulatif</td>
		<td align="center">=</td>
		<td><?= $row['ipk'] ?></td>
		
	</tr>
</table>

<br>&nbsp;
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td width="387">&nbsp;</td>
		<td class="mark">Bandung, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>Ketua Program Studi
	</tr>
	<tr>
		<td class="pad">Dosen Wali</td>
		<td><?= $row['namaunit'] ?></td>
	</tr>
	<tr height="45">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td class="mark pad"><strong><u><?= $row['dosenwali'] ?></u></strong></td>
		<td class="mark"><strong><u><?= $row['ketua'] ?></u></strong></td>
	</tr>
</table>
<div style="height:5px"></div>
<div style="height:5px"></div>
<?php /*
<div class="div_subhead" align="left" style="width:<?= $p_tbwidth ?>px">Detil Nilai</div>
<table class="tb_subfoot" width="<?= $p_tbwidth ?>">
<?php
	$i=0;
	if(isset($row['unsur']))
	foreach($row['unsur'] as $matakuliah=>$rowunsur){
		$i++;
?>
	<tr>
		<td align="center"><?= $i ?></td>
		<td colspan="4"><?= $matakuliah ?></td>
	</tr>
	<?php foreach($rowunsur as $idunsur=>$rowu){ ?>
	<tr>
		<td width="30" align="center">&nbsp;</td>
		<td width="80"><?= $rowu['namaunsurnilai'] ?></td>
		<td width="30"><?= $rowu['prosentasenilai'] ?>%</td>
		<td width="30"><?= $rowu['nilaiunsur'] ?></td>
		<td>&nbsp;</td>
	</tr>
<?php } ?>
	<tr>
		<td width="30" align="center">&nbsp;</td>
		<td colspan="2">Nilai Akhir</td>
		<td width="30"><?=$rowunsur[$idunsur]['nnumerik'] ?></td>
		<td>&nbsp;</td>
	</tr>
<?php 
}?> */ ?>
<?php
	}
?>
</div>
</body>
</html>
