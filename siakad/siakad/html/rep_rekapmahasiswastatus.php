<?php

	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// cek apakah sudah login
	if(!Modul::isAuthenticated()){//echo "b".die();
		Route::redirect($conf['menu_path']);}

	require_once(Route::getModelPath('mahasiswa'));

	$p_tbwidth = 750;

	$p_title = 'REKAPITULASI MAHASISWA STIE INABA '.Akademik::getNamaPeriode($r_periode);
	$rekap = mMahasiswa::getStatusAllMahasiswa($conn);
	$p_detailpage = "rep_rekapmhstatusprodiall";
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
		<table class="tb_data" width="<?= $p_tbwidth ?>">
			<tr>
				<th style="text-align: left">Status</th>
				<th>Jumlah</th>
				<th>#</th>
			</tr>
			<?php foreach ($rekap as  $value) { $t_key = str_replace(str_split('{ }'), '',$value['status']);?>
				<?php $total = $value['jumlah']+$total ?>
			<tr>
				<td>
					<?php if($value['status'] == "{A}"){ ?>
						Aktif
					<?php }elseif($value['status'] == "{T}"){ ?>
						Tidak Aktif
					<?php }elseif($value['status'] == "{C}"){ ?>
						Cuti
					<?php }elseif($value['status'] == "{K}"){ ?>
						Keluar
					<?php }elseif($value['status'] == "{L}"){ ?>
						Lulus
					<?php }elseif($value['status'] == "{O}"){ ?>
						Drop Out
					<?php } ?>	
				</td>
				<td><center><b><?= $value['jumlah']?></b></center></td>
				<td><center><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/search.png" onclick="goDetail(this)" target="_BLANK" style="cursor:pointer"></center></td>
			</tr>
			
			<?php } ?>
			<tr>
				<th style="text-align: left"><b>Total</b></th>
				<th style="text-align: center"><b><?= $total; ?></b></th>
				<th></th>
			</tr>
		</table>
	</div>
	<script type="text/javascript">

	var detailpage = "<?= Route::navAddress('rep_rekapmhstatusprodiall') ?>";
	</script>
</body>
</html>