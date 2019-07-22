<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporanmhs'));
	require_once(Route::getModelPath('combo'));
	
	// variabel request
	$r_unit = CStr::removeSpecial(CStr::formatDate($_REQUEST['unit']));
	$r_angkatan = CStr::removeSpecial(CStr::formatDate($_REQUEST['angkatan']));
	$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	$r_format = $_REQUEST['format'];
	
	
	// properti halaman
	$p_title = 'REKAPITULASI KRS MAHASISWA PERIODE '.strtoupper(Akademik::getNamaPeriode($r_periode));
	$p_tbwidth = 650;
	$p_namafile = 'rekapkrs_'.$r_periode;
	
	$rs=mLaporanMhs::getRekapKrs($conn,$r_unit,$r_periode,$r_angkatan);
	
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
		.tb_head td, .div_head { font-size: 14px }
		.div_subhead { font-size: 11px; margin-bottom: 5px }
		
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 12px; padding: 2px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Tahoma, Arial }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }
	</style>
</head>
<body>
<div align="center">
<?php if(!empty($a_data)) include('inc_headerlap.php'); ?>

	<div class="div_head"><?=$p_title?></div>
	<br>
	<table class="tb_head" width="<?= $p_tbwidth ?>">
		<tr>
			<td>Unit</td>
			<td>:</td>
			<td><?=mUnit::getNamaUnit($conn,$r_unit)?></td>
		</tr>
		<tr>
			<td>Angkatan</td>
			<td>:</td>
			<td><?=$r_angkatan?></td>
		</tr>
	</table>
	<br>
	<table class="tb_data" width="<?= $p_tbwidth ?>">
		<thead>
		<tr>
			<th  width="15">No</th>
			<th  >Prodi</th>
			<th  width="50">Jml Mhs</th>
			<th  width="50">Jml Sudah KRS</th>
			<th  width="50">Jml Belum KRS</th>
		</tr>
		</thead>
		<tbody>
		<?php 
		$no=0;
		while($row=$rs->fetchRow()){ 
		$no++;
		?>
			<tr>
				<td align="center"><?=$no?></td>
				<td ><?=$row['jurusan']?></td>
				<td align="center"><?=$row['jum_mhs']?></td>
				<td align="center"><?=$row['jum_krs']?></td>
				<td align="center"><?=$row['jum_blmkrs']?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>
</body>
</html>
