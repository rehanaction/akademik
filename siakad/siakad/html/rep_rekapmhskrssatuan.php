<?php

	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// /$conn->debug=true;
	// cek apakah sudah login
	if(!Modul::isAuthenticated()){//echo "b".die();
		Route::redirect($conf['menu_path']);}

	require_once(Route::getModelPath('mahasiswa'));

	$p_tbwidth = 750;

	$r_key=CStr::removeSpecial($_REQUEST['key']);

	$rekap = mMahasiswa::getStatusmhskrs($conn, $r_key);
	//$p_detailpage = "rep_rekapmhskrssatuan";

	$nama = Akademik::getNamaMahasiswa($conn, $r_key);
	$bio = mMahasiswa::getDatamhs($conn, $r_key);

	$p_title = 'REKAPITULASI KRS MAHASISWA'.' '.Akademik::getNamaPeriode($r_periode);
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
		.tb_head td, .div_head { font-size: 12px }
		.div_subhead { font-size: 11px; margin-bottom: 5px }
		
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 10px; padding: 2px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Tahoma, Arial }

		.tab_data { border-collapse: collapse }
		.tab_data th, .tab_data td {font-size: 10px; padding: 2px }
		.tab_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tab_data td { font-family: Tahoma, Arial }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }

	</style>
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
	<div align="center">
	<?php include('inc_headerlap.php'); ?>
		<div class="div_head"><?=strtoupper($p_title)?></div>
		<br>
		<table width="<?= $p_tbwidth ?>" class="tab_data" >
			<tr>
				<td>NIM</td>
				<td>:</td>
				<td><?= $bio['nim'] ?></td>
			</tr>
			<tr>
				<td>Nama</td>
				<td>:</td>
				<td><?= $bio['nama'] ?></td>
			</tr>
			<tr>
				<td>Program Studi</td>
				<td>:</td>
				<td><?= $bio['namaunit'] ?></td>
			</tr>
			<tr>
				<td>Basis</td>
				<td>:</td>
				<td><?= $bio['namasistem'] ?></td>
			</tr>
		</table>
		<br>
		<table class="tb_data" width="<?= $p_tbwidth ?>" align="center">

			<tr>
				<th>No.</th>
				<th>Kode Matakuliah</th>
				<th style="text-align: left">Matakuliah</th>
				<th>SKS</th>
				<th>Kelas</th>
				<th>Perkuliahan</th>
			</tr>
			<?php $no= 1; foreach ($rekap as  $value) { ?>
				<?php $total = $value['sks']+$total ?>
				<tr>
					<td style="text-align: center"><?= $no; ?></td>
					<td style="text-align: center"><?= $value['kodemk'] ?></td>
					<td style="text-align: left"><?= $value['namamk'] ?></td>
					<td style="text-align: center"><?= $value['sks'] ?></td>
					<td style="text-align: center"><?= $value['kelasmk'] ?></td>
					<td style="text-align: center">
						<?php if ($value['isonline'] == -1) {
							echo "Online";
						}else{
							echo "Tatap Muka";
						} ?>
					</td>
					
				</tr>
			<?php $no++; } ?>
				<tr>
					<th style="text-align: right" colspan="3"><b>Total SKS</b></th>
					<th style="text-align: center"><b><?= $total; ?></b></th>
					<th colspan="2"></th>
				</tr>
		</table>
	</div>
	
</body>
</html>