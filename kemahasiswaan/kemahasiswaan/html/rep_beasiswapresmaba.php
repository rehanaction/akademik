<?php

	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	Modul::getFileAuth();

	// variabel request
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester =CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun =  CStr::removeSpecial($_REQUEST['tahun']);
	$r_propinsi = CStr::removeSpecial($_REQUEST['propinsi']);
	$r_prestasi = CStr::removeSpecial($_REQUEST['prestasi']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	$r_periode=$r_tahun.$r_semester;
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('mhsasuransi'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('settingmhs'));

	//$i_mhs = mMahasiswa::getDataSingkat($conn,$r_unit,$r_semester,$r_propinsi);

	// properti halaman
	$p_title = 'Daftar Pengajuan Beasiswa Pendaftar';
	$p_tbwidth = 700;
	$p_namafile = 'rekap_beasiswaprestasimaba';

	$a_data = mLaporan::getBeasiswaPresMaba($conn,$r_unit,$r_tahun.$r_semester,$r_propinsi,$r_prestasi);
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
<div align="center" style="width:<?= $p_tbwidth ?>px"><b><?=$p_title?></b></div>
<br>
 <table  border ="0" width="<?= $p_tbwidth ?>">
  <tr valign ="top">
     <td width="75">Prodi</td>
     <td align="center" width="10">:</td>
	 <td width="310" class="mark"><?= mUnit::getNamaUnit($conn,$r_unit) ?></td>
	 </tr>
	 <tr valign ="top">
	 <td>Periode Semester</td>
     <td align="center" width="10">:</td>
     <td>
	 <?
	if ($r_semester == "1")
	{
	echo "Ganjil";
	}
	elseif($r_semester == "2")
	{
	echo "Genap";
	}
	elseif($r_semester == "3")
	{
	echo "Pendek";
	}?>
	 /<?= $r_tahun; ?></td>
   </tr></table>
<br>
<table width="<?= $p_tbwidth ?>" class="tb_data">
<thead>
<tr>

    <th width="80" >No.</th>
	<th  >Nomor Pendaftar</th>
	<th width="100" >Nama</th>
	<th width="100" >Pilihan 1</th>
	<th width="100" >Pilihan 2</th>
	<th width="100" >Beasiswa</th>
	<th width="100" >Provinsi</th>
	<th width="100" >Prestasi</th>
	<th width="100" >Status</th>

</tr>
</thead>
<tbody>
	<?
	$i=0;
	$total_honor=0;
	$total_honorp=0;
	$total_pajak=0;
	foreach($a_data as $row){
	?>
	<tr>

	<td><?=++$i?></td>
	<td><?=$row['nopendaftar']?></td>
	<td><?=$row['nama']?></td>
	<td><?=$row['pilihan1']?></td>
	<td><?=$row['pilihan2']?></td>
	<td><?=$row['namabeasiswa']?></td>
	<td><?=$row['namapropinsi']?></td>
	<td><?=$row['prestasi']?></td>
	<td><?=(!empty($row['isditerima']))? 'Diterima':'' ?></td>
	</tr>
	<? }?>

	</tbody>
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
				<td width="<?= $p_tbwidth/3 ?>">Kabiro Pengembangan Kerjasama Institusi</td>
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
				<td><?=$a_pejabat['namabeasiswa']?><hr>Nip. <?=$a_pejabat['nipbeasiswa']?></td>
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
