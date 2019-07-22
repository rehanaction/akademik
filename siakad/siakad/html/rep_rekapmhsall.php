<?php

	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// /$conn->debug=true;
	// cek apakah sudah login
	if(!Modul::isAuthenticated()){//echo "b".die();
		Route::redirect($conf['menu_path']);}

	require_once(Route::getModelPath('mahasiswa'));

	$p_tbwidth = 750;

	$r_key=CStr::removeSpecial($_REQUEST['key']);

	list($kodeunit,$statusmhs,$sistemkuliah)=explode('|',$r_key);
	$r_key=$kodeunit.$statusmhs.$sistemkuliah;

	$rekap = mMahasiswa::getStatusAllMahasiswaAllas($conn, $kodeunit, $statusmhs, $sistemkuliah);
	$p_detailpage = "rep_rekapmhskrssatuan";

	$namaprodi = Akademik::getNamaUnit($conn, $kodeunit);

	if ($statusmhs == "A") {
		$statusnya = "Aktif";
	}elseif ($statusmhs == "K") {
		$statusnya = "Keluar";
	}elseif ($statusmhs == "T") {
		$statusnya = "Tidak Aktif";
	}elseif ($statusmhs == "C") {
		$statusnya = "Cuti";
	}elseif ($statusmhs == "O") {
		$statusnya = "Drop Out";
	}elseif ($statusmhs == "L") {
		$statusnya = "Lulus";
	}

	if ($sistemkuliah == "RS") {
		$sistemnya = "Reguler Sore";
	}elseif ($statusmhs == "R") {
		$sistemnya = "Reguler Pagi";
	}

	$p_title = 'REKAPITULASI MAHASISWA Program Studi '.$namaprodi.' '.$sistemnya.' '.Akademik::getNamaPeriode($r_periode).' STATUS '. $statusnya;
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
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }

	</style>
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
	<div align="center">
	<?php include('inc_headerlap.php'); ?>
		<div class="div_head"><?=strtoupper($p_title)?></div>
		<br>
		<table class="tb_data" width="<?= $p_tbwidth ?>" align="center">

			<tr>
				<th>No.</th>
				<th>NIM</th>
				<th style="text-align: left">Nama</th>
				<th>Angkatan</th>
				<th>SKS</th>
				<th>IPK</th>
				<th>SKS Lulus</th>
				<th>IPK Lulus</th>
				<th>Semester</th>
				<th>KRS</th>
				<th>Lihat KRS</th>
			</tr>
			<?php $no= 1; foreach ($rekap as  $value) { $t_key = $value['nim']; ?>
				
				<tr>
					<td style="text-align: center"><?= $no; ?></td>
					<td style="text-align: center"><?= $value['nim'] ?></td>
					<td><?= $value['nama'] ?></td>
					<td style="text-align: center"><?= $value['angkatan'] ?></td>
					<td style="text-align: center"><?= $value['sks'] ?></td>
					<td style="text-align: center"><?= $value['ipk'] ?></td>
					<td style="text-align: center"><?= $value['skslulus'] ?></td>
					<td style="text-align: center"><?= $value['ipklulus'] ?></td>
					<td style="text-align: center"><?= $value['semmhs'] ?></td>
					<td style="text-align: center;">
						<?php if ($value['frsterisi'] == -1) { ?>
							<img src="images/check.png">
						<?php }elseif ($value['frsterisi'] == 0) { ?>
							<img src="images/delete.png">
						<?php }  ?>
					</td>
					<td><center><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/search.png" onclick="goDetail(this)" target="_BLANK" style="cursor:pointer"></center></td>
				</tr>
			<?php $no++; } ?>
			
		</table>
	</div>
	<script type="text/javascript">

	var detailpage = "<?= Route::navAddress('rep_rekapmhskrssatuan') ?>";
	</script>
</body>
</html>