<?php

	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=false;
	// hak akses
	Modul::getFileAuth();

	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['jurusan']);
	$r_tglawal = CStr::removeSpecial($_REQUEST['tglawal']);
	$r_tglakhir = CStr::removeSpecial($_REQUEST['tglakhir']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	$r_periode=$r_tahun.$r_semester;
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('settingmhs'));

	$namaunit = mUnit::getNamaUnit($conn,$r_kodeunit);

	// properti halaman
	$p_title = 'Laporan Rekapitulasi Klaim Asuransi <br>'.$namaunit;
	$p_tbwidth = 950;
	$p_namafile = 'rekap_klaim'.$r_tglawal.' - '.$r_tglakhir.'-'.$namaunit;

	$a_data = mLaporan::getRekapKlaim($conn,$r_kodeunit,CStr::formatDate($r_tglawal),CStr::formatDate($r_tglakhir));
	$a_pejabat = mSettingMhs::getData($conn,1);
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
	<br>
	<table width="<?= $p_tbwidth ?>" style="border:solid 1px; font-family: Arial; font-size:12pt; border-collapse:collapse" cellpadding="5" cellspacing="5">
	<thead>
	<tr style="background-color: #CFC; border:solid 1px">
		<th width="80">No.</th>
		<th align="left">Nama Unit</th>
		<th width="100" >Daftar Klaim</th>
		<th width="100" >Jumlah Diterima</th>

	</tr>
	</thead>
	<tbody>
		
		<?
		$i=0;
		foreach($a_data['sum'] as $row){
			$total += $row['daftarklaim'];
			$totalterima += $row['jumlahditerima'];
		?>
		<tr style="background:#f0f1f2">
			<td><?=++$i?></td>
			<td><?=$row['namaunit']?></td>
			<td align="center"><?=$row['daftarklaim']?></td>
			<td align="center"><?=$row['jumlahditerima']?></td>
		</tr>
		<tr>
			<td colspan="4">
				<table width="100%" border="1" style="border-collapse:collapse" cellpadding="5">
					<tr>
						<th width="150">Nim</th>
						<th align="left">Nama</th>
					</tr>
					<?php foreach ($a_data['detail'][$row['kodeunit']] as $mhs){ ?>
					<tr style="border:0">
						<td><?php echo $mhs['nim']?></td>
						<td><?php echo $mhs['nama']?></td>
					</tr>
					<?php } ?>
				</table>
			</td>
		</tr>
		<? }?>
		<tr style="background:#f0f1f2">
			<td align="center" colspan="2"><b>Total</b></td>
			<td align="center"><b><?=number_format($total,0,',','.')?></b></td>
			<td align="center"><b><?=number_format($totalterima,0,',','.')?></b></td>
		</tr>
	</tbody>
	</table>
	<br>
	<div id="foot">
		<table class="tb_foot tb_foot_ttd" width="<?= $p_tbwidth ?>" style="font-size:12pt">
			<tr>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>">Jakarta, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
			</tr>
			<tr>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>">Kepala Biro Kemahasiswaan</td>
			</tr>
			<tr>
				<td colspan="3" height="50">&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><?=$a_pejabat['namakabiro']?><hr>Nip. <?= $a_pejabat['nipkabiro']?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<br>
	</div>

	</div>
 </body>
 </html>
