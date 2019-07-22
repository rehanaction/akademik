<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('ekivaturan'));
	
	// variabel request
	$r_nim = CStr::removeSpecial($_REQUEST['nim']);
	$r_kurikulum = CStr::removeSpecial($_REQUEST['kurikulumbaru']);
	$r_format = $_REQUEST['format'];
	
	// cek anak wali
	if(!empty($r_nim) and Akademik::isDosen()) {
		$cek = mMahasiswa::isDosenWali($conn,$r_nim,Modul::getUserName());
		if(empty($cek))
			unset($r_nim);
	}
	
	// properti halaman
	$p_title = 'Hasil Ekivalensi Mata Kuliah';
	$p_tbwidth = 720;
	$p_namafile = 'ekivalensi_'.$r_kurikulum.'_'.$r_nim;
	
	// mengambil data
	$row = mMahasiswa::getDataSingkat($conn,$r_nim);
	
	// sisi kiri
	$rows = mEkivAturan::getListKiri($conn,$r_nim,$row['kodeunit']);
	
	$skskiri = 0;
	foreach($rows as $rowkiri) {
		$skskiri += (int)$rowkiri['sks'];
		
		$kur[$rowkiri['semester']][] = $rowkiri['thnkurikulum'];
		$kodemk[$rowkiri['semester']][] = $rowkiri['kodemk'];
		$namamk[$rowkiri['semester']][] = $rowkiri['namamk'];
		$sks[$rowkiri['semester']][] = $rowkiri['sks'];
		$nhuruf[$rowkiri['semester']][] = $rowkiri['nhuruf'];
	}
	
	// sisi kanan
	$rows = mEkivAturan::getListKanan($conn,$r_nim,$row['kodeunit'],$r_kurikulum);
	
	$skskanan = 0;
	foreach($rows as $rowkanan) {
		$a_sem[$rowkanan['semmk']] = $rowkanan['semmk'];
		if(!empty($rowkanan['statusekivalen']))
			$skskanan += (int)$rowkanan['sks'];
		
		$kodemkA[$rowkanan['semmk']][] = $rowkanan['kodemk'];
		$namamkA[$rowkanan['semmk']][] = $rowkanan['namamk'];
		$sksA[$rowkanan['semmk']][] = $rowkanan['sks'];
		$statusA[$rowkanan['semmk']][] = $rowkanan['statusekivalen'];
		$nhurufA[$rowkanan['semmk']][] = $rowkanan['wajibpilihan'];
	}
	
	ksort($a_sem);
	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		/* Dari KHS */
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
		
		/* Baru */
		.tb_noborder > tbody > tr > th, .tb_noborder > tbody > tr > td { font-size: 11px; padding-top: 10px; vertical-align: top }
		.tb_noborder > tbody > tr > tr > td:first-child { padding-right: 10px }
		.tb_noborder > tbody > tr > td:last-child { padding-left: 10px }
	</style>
</head>
<body>
<div align="center">
<?php include('inc_headerlap.php'); ?>
<div class="div_head">Hasil Ekivalensi Mata Kuliah</div>
<div class="div_subhead">Kurikulum <?php echo $r_kurikulum ?></div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr valign="top">
		<td width="70"><strong>N I M</strong></td>
		<td align="center" width="10">:</td>
		<td width="300" class="mark"><?= $row['nim'] ?></td>
		<td width="70"><strong>Prodi</strong>
		<td align="center" width="10">:</td>
		<td><?= $row['jurusan'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Nama</strong></td>
		<td align="center">:</td>
		<td class="mark"><strong><?= $row['nama'] ?></strong></td>
		<td><strong>Periode Masuk</strong></td>
		<td align="center">:</td>
		<td><?= $row['namaperiodedaftar'] ?></td>
	</tr>
</table>
<div style="height:5px"></div>
<table class="tb_noborder" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="50%" align="center">Kurikulum Lama</th>
		<th align="center">Kurikulum <?php echo $r_kurikulum ?></th>
	</tr>
	<?php foreach($a_sem as $sem) { ?>
	<tr>
		<td>
<table class="tb_data" style="width:100%">
	<tr>
		<th colspan="3">Semester <?php echo $sem ?></th>
	</tr>
	<tr>
		<th width="60">Kurikulum</th>
		<th>Matakuliah</th>
		<th width="30">Nilai</th>
	</tr>
	<?php for($i=0;$i<count($kodemk[$sem]);$i++) { ?>
	<tr>
		<td align="center"><?php echo $kur[$sem][$i] ?></td>
		<td><?php echo $kodemk[$sem][$i].' | '.$namamk[$sem][$i].' | '.$sks[$sem][$i].' sks' ?></td>
		<td align="center"><?php echo $nhuruf[$sem][$i] ?></td>
	</tr>
	<?php } ?>
</table>
		</td>
		<td>
<table class="tb_data" style="width:100%">
	<tr>
		<th colspan="3">Semester <?php echo $sem ?></th>
	</tr>
	<tr>
		<th width="30"></th>
		<th>Matakuliah</th>
		<th width="30">P/W</th>
	</tr>
	<?php for($i=0;$i<count($kodemkA[$sem]);$i++) { ?>
	<tr>
		<td align="center"><?php echo ($statusA[$sem][$i]) ? 'V' : '' ?></td>
		<td><?php echo $kodemkA[$sem][$i].' | '.$namamkA[$sem][$i].' | '.$sksA[$sem][$i].' sks' ?></td>
		<td align="center"><?php echo $nhurufA[$sem][$i] ?></td>
	</tr>
	<?php } ?>
</table>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<th>Total SKS yang sudah ditempuh : <?php echo $skskiri ?></th>
		<th>Total SKS hasil ekivalensi : <?php echo $skskanan ?></th>
	</tr>
</table>
</div>
</body>
</html>
