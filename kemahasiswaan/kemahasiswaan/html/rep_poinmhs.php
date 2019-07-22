<?php

	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=false;
	// hak akses
	Modul::getFileAuth();

	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['jurusan']);
	$r_tahun= CStr::removeSpecial($_REQUEST['tahun']);
	$r_semester= CStr::removeSpecial($_REQUEST['semester']);
	$r_nim = CStr::removeSpecial($_REQUEST['nim']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	$r_periode=$r_tahun.$r_semester;
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('settingmhs'));

	$namaunit = mUnit::getNamaUnit($conn,$r_kodeunit);

	// properti halaman
	$p_title = 'Laporan Poin Mahasiswa <br>'.$namaunit;
	$p_tbwidth = 700;
	$p_namafile = 'Periode '.$r_periode;

	$a_data = mLaporan::getPoinByPeriode($conn,$r_kodeunit,$r_periode);
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
<table width="<?= $p_tbwidth ?>" class="tb_data">
<thead>
<tr bgcolor= 'green'>

  <th width="30" >No.</th>
	<th  >NIM</th>
	<th  >Nama</th>
	<th width="30" >Prestasi</th>
	<th width="30" >Aktivitas</th>
	<th width="30" >Pelanggaran</th>
	<th width="30" >Total</th>
</tr>
</thead>
<tbody>
	<?
	$i=0;
	$total_honor=0;
	$total_honorp=0;
	$total_pajak=0;
	foreach($a_data as $row){
		$total = $row['prestasi']+$row['kegiatan']-$row['pelanggaran'];
	?>
	<tr>

	<td><?=++$i?></td>
	<td><?=$row['nim']?></td>
	<td><?=$row['nama']?></td>
	<td><?=$row['prestasi']?></td>
	<td><?=$row['kegiatan']?></td>
	<td><?=$row['pelanggaran']?></td>
	<td><?=$total?></td>
	</tr>
	<? }?>
	</tbody>
	</table>
	<br>
	<div id="foot">
		<table class="tb_foot tb_foot_ttd" width="<?= $p_tbwidth ?>">
			<tr>
				<td colspan="3"> <b><i>* Perhitungan poin : poin prestasi + poin aktivitas - poin pelanggaran.</i></b> </td>
			</tr>
			<tr>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>">Jakarta, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
			</tr>
			<tr>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>"></td>
				<td width="<?= $p_tbwidth/3 ?>">KKabiro Pengembangan Kerjasama Institusi</td>
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
