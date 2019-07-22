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
	
	$r_periode = Akademik::getPeriode();
	
	// properti halaman
	$p_title = 'Kemajuan Belajar / Daftar Prestasi';
	$p_tbwidth = 720;
	
	if(empty($r_npm)) {
		$p_namafile = 'transkrip_akademik_'.$r_periode.'_'.$r_kodeunit.'_'.$r_angkatan;
		$a_data = mLaporanMhs::getTranskripSementaraUnit($conn,$r_periode,$r_kodeunit,$r_angkatan);
	}
	else {
		$p_namafile = 'transkrip_akademik_'.$r_npm;
		$a_data = mLaporanMhs::getTranskripSementara($conn,$r_periode,$r_npm);
	}
	
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
		
		.tb_head td, .div_head { font-family: "Times New Roman" }
		.tb_head td { font-size: 10px }
		.div_head { font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 5px }
		
		.tb_cont td { padding: 0; vertical-align: top }
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; font-size: 8px; padding: 1px }
		.tb_data th { background-color: #FFF }
		.tb_data .mark { font-family: "Arial Narrow","Arial" }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 11px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body>
<div align="center">
<?php
	$tsks = 0;
	$tbobot = 0;
	$m = count($a_data);
	for($c=0;$c<$m;$c++) {
		$row = $a_data[$c];
		
		include('inc_headerlap.php');
?>
<div class="div_head"><b>TRANSKRIP AKADEMIK</b></div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr valign="top">
		<td width="150"><strong>NAMA</strong></td>
		<td align="center" width="10">:</td>
		<td width="200"><strong><?= $row['nama'] ?></strong></td>
		<td width="100"><strong>PROGRAM STUDI</td>
		<td align="center" width="10">:</td>
		<td><?= $row['programpend'] ?> - <?= $row['namaunit'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>NIM</strong></td>
		<td align="center">:</td>
		<td><?= $row['nim'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>TEMPAT, TANGGAL LAHIR</strong></td>
		<td align="center">:</td>
		<td colspan="4"><?= $row['tmplahir'] ?>, <?= CStr::formatDateInd($row['tgllahir']) ?></td>
	</tr>
</table>
<div style="height:5px"></div>
<table class="tb_cont" width="<?= $p_tbwidth ?>">
	<tr>
<?php
	$j = 0;
	for($s=0;$s<2;$s++) {
?>

<td width="49%">
<table class="tb_data" width="100%">
	<tr>

		<th width="12">No</th>
		<th width="25">Kode</th>
		<th>Matakuliah</th>
		<th width="25">SKS</th>
		<th width="25">Nilai</th>
		<th width="30">Bobot</th>
		<th>Smt</th>
	</tr>
	
<?php
		$do = $row['transkrip'];
		$n = count($row['transkrip'][$s]);
		for($i=0;$i<$n;$i++) {
			$rowt = $row['transkrip'][$s][$i];
			$t_smt = $rowt['semester'];
			$t_nangka = $rowt['nangka'];
			$t_sks = $rowt['sks'];
			
			$t_nsks = $t_nangka*$t_sks;
			$t_tsks += $t_sks;
			$t_tnsks += $t_nsks;
			if(is_array($rowt)) {
?> 
	
	<tr height="14">
		<td align="center"><?= ++$j ?></td>
		<td align="center"><?= $rowt['kodemk'] ?></td>
		<td class="mark"><?= $rowt['namamk'] ?></td>
		<td align="center"><?= $rowt['sks'] ?></td>
		<td align="center"><?= $rowt['nhuruf'] ?></td>
		<td align="center"><?= $rowt['sks']*$rowt['nangka'] ?></td>
		<td align="center"><?= $rowt['semester'] ?></td>
		
		
	</tr>
	
	<?php 
		  $tsks += $rowt['sks'];
		  $tmutu += $rowt['sks']*$rowt['nangka'] ; 
		  $ipk = round($tmutu/$tsks,2); 
		?>
<?php
			}
			else { 
?>
	
<?php
			}
		}
?>

</table>
		</td>
<?php
		if($s == 0) {
?>
		<td width="2%">&nbsp;</td>
<?php
		}
	}
?>
	</tr>
	
</table>
<br>

<table class="tb_subfoot" width="<?= $p_tbwidth ?>">
	<tr>
		<td width="200">JUMLAH MUTU</td>
		<td width="10">:</td>
		<td><b><?= $t_tnsks ?></b></td>
	</tr>
	<tr>
		<td>JUMLAH KREDIT KUMULATIF</td>
		<td width="10">:</td>
		<td><b><?= $tsks ?></b></td>
	</tr>
	<tr>
		<td>INDEX PRESTASI KOMULATIF</td>
		<td width="10">:</td>
		<td><b><?= $ipk ?></b></td>
	</tr>
	<tr>
		<td>PREDIKAT KELULUSAN</td>
		<td width="10">:</td>
		<td><b>
			<?php
				if($ipk >="3.51"){
					echo "Dengan Pujian";
				}elseif ($ipk >="3.01") {
					echo "Sangat Memuaskan";
				}elseif ($ipk >="2.76") {
					echo "Memuaskan";
				}elseif($ipk >="2.00"){
					echo "Baik";
				}
			?>
			
		</td></b>
	</tr>
</table>

<div style="height:5px"></div>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td width="450">&nbsp;</td>
		<td class="mark">Bandung, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
	<tr>
		<td>Ketua STIE INABA</td>
		<td>Ketua Program Studi,</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><?= $row['namaunit'] ?></td>
	</tr>
	<tr height="45">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td><u>Dr. YOYO SUDARYO,S.E.,M.M.,Ak,CA</u></td>
		<td><u><?= $row['ketua'] ?></u></td>
	</tr>
	
</table>
<?php
	}
?>
</div>
</body>
</html>