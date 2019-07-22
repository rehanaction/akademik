<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	Modul::getFileAuth();

	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['jurusan']);
	$r_tahun= CStr::removeSpecial($_REQUEST['tahun']);
	$r_semester= CStr::removeSpecial($_REQUEST['semester']);
	$r_nim = CStr::removeSpecial($_REQUEST['nim']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	$jeniskegiatan = array('E'=>'External','I'=>'Internal');

	$r_periode=$r_tahun.$r_semester;

	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('periode'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('settingmhs'));

	$periode = mPeriode::getArray($conn);
	
	$namaunit = mUnit::getNamaUnit($conn,$r_kodeunit);

	// properti halaman
	$p_title = 'Transkrip Softskill'.$namaunit;
	$p_tbwidth = 700;
	$p_namafile = 'Periode '.$r_periode;

	$a_data = mLaporan::getDataTranskripSoftskill($conn,$r_nim);
	$a_data2 = mLaporan::getDataTranskripSoftskillPrestasi($conn,$r_nim);
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
<div style="width:<?= $p_tbwidth ?>px"><h2><b><?=$p_title?></b></h2></div>
<div style="width:<?= $p_tbwidth ?>px" align="left">
<table width="<?= $p_tbwidth ?>px">
	<tr>
		<td>Nim</td>
		<td>:</td>
		<td><?=$a_data[0]['nim']?></td>
	</tr>
	<tr>
		<td>Nama</td>
		<td>:</td>
		<td><?=$a_data[0]['namamhs']?></td>
	</tr>
	<tr>
		<td>Fakultas</td>
		<td>:</td>
		<td><?=$a_data[0]['namafakultas']?></td>
	</tr>
	<tr>
		<td>Program Studi</td>
		<td>:</td>
		<td><?=$a_data[0]['namaprodi']?></td>
	</tr>
	<tr>
		<td>Tempat, Tanggal Lahir</td>
		<td>:</td>
		<td><?=$a_data[0]['tmplahir']?>, <?=cstr::formatDateInd($a_data[0]['tgllahir'])?></td>
	</tr>
</table>

</div>
<h4 align="left" style="width:<?= $p_tbwidth ?>px">Aktivitas Mahasiswa</h4>
<table width="<?= $p_tbwidth ?>" class="tb_data">
<thead>
<tr bgcolor= 'green'>

  <th width="10" >No.</th>
	<th width="10" >Nama Kegiatan</th>
	<th width="10" >Jenis Kegiatan</th>
	<th width="30" >Waktu Pelaksanaan</th>
	<th width="30" >Peran Kegiatan</th>
	<th width="60" >Skala Kegiatan</th>
	<th width="30" >Poin</th>
</tr>
</thead>
<tbody>
	<?
	$i=0;
	$total_kegiatan=0;
	foreach($a_data as $row){
		$total_kegiatan += $row['poinkegiatan'] ;
	?>
	<tr>

	<td align="center"><?=++$i?></td>
	<td><?=$row['namakegiatan']?></td>
	<td align="center"><?=$jeniskegiatan[$row['jenisaktivitas']]?></td>
	<td><?=cstr::formatDateInd($row['tglkegiatan'])?> s/d <?=cstr::formatDateInd($row['tglkegiatanakhir'])?></td>
	<td><?=$row['tingkatkegiatan']?></td>
	<td><?=$row['peran']?></td>
	<td align="center"><?=$row['poinkegiatan']?></td>
	</tr>
	<? }?>
	<tr>
		<td colspan="6">Jumlah</td>
		<td colspan="6" align="center"><?=$total_kegiatan?></td>
	</tr>
	</tbody>
	</table>
	<br>
<h4 align="left" style="width:<?= $p_tbwidth ?>px">Prestasi Mahasiswa</h4>
<table width="<?= $p_tbwidth ?>" class="tb_data">
<thead>
<tr bgcolor= 'green'>

  <th width="10" >No.</th>
	<th  >Nama Kompetisi</th>
	<th  >Jenis Peserta</th>
	<th width="30" >Waktu Pelaksanaan</th>
	<th width="30" >Penyelenggara</th>
	<th width="30" >Tingkat Kompetisi</th>
	<th width="30" >Capaian</th>
	<th width="30" >Poin</th>
</tr>
</thead>
<tbody>
	<?
	$i=0;
	$total_prestasi=0;
	foreach($a_data2 as $row){
		$total_prestasi += $row['poin'];
		
	?>
	<tr>

	<td align="center"><?=++$i?></td>
	<td><?=$row['namaprestasi']?></td>
	<td align="center"><?=$row['namajenispeserta']?></td>
	<td><?=cstr::formatDateInd($row['tglprestasi'])?> s/d <?=cstr::formatDateInd($row['tglprestasiakhir'])?></td>
	<td align="center"><?=$row['penyelenggara']?></td>
	<td align="center"><?=$row['namatingkatprestasi']?></td>
	<td align="center"><?=$row['namakategoriprestasi']?></td>
	<td align="center"><?=$row['poin']?></td>
	</tr>
	<? }?>
	<tr>
		<td colspan="7">Jumlah</td>
		<td align="center"><?= $total_prestasi?></td>
	</tr>
	</tbody>
	</table>
	<br>
	<table class="tb_data">
			<tr>
				<td>Jumlah Poin Aktivitas</td>
				<td align="right"><?=$total_kegiatan?></td>
			</tr>
			<tr>
				<td>Jumlah Poin Prestasi</td>
				<td align="right"><?=$total_prestasi?></td>
			</tr>
			<tr>
				<td>Jumlah Poin Keseluruhan</td>
				<td align="right"><?=$total_kegiatan + $total_prestasi?></td>
			</tr>
		</table>
		<br>
	<div id="foot">
		<table class="tb_foot tb_foot_ttd" width="<?= $p_tbwidth ?>">
			
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
				<td><u><?=$a_pejabat['namakabiro']?></u></br>NIP. <?=$a_pejabat['nipkabiro']?></td>

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
