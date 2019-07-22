<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_jurusan = CStr::removeSpecial($_REQUEST['jurusan']);
	$tglawal = $_POST["tglawal"];
	$tglakhir = $_POST["tglakhir"];
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	

	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	
	// properti halaman
	$p_title = 'Laporan Jadwal Pelaksanaan Ujian Skripsi';
	$p_tbwidth = 900;
	$p_namafile = 'jadwal_skripsi_'.$r_kodeunit;
	
	$rs = mLaporan::getJadwalSkripsi($conn,$r_jurusan,$tglawal,$tglakhir);
	 
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
		.div_preheader { font-size: 15px; font-weight: bold }
		.div_header { font-size: 15px }
		.div_headertext { font-size: 12px; font-style: italic }
		
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
		.tb_data td { font-size: 20px }
		.tb_data .noborder th { border-left: none; border-right: none }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 15px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body>
<div align="center">
<?php
	
		include('inc_headerlap.php');
?>
<div class="div_head">LAPORAN JADWAL PELAKSANAAN UJIAN SKRIPSI</div>
</br>
 <table  border ="0" width="<?= $p_tbwidth ?>">
 <tr valign ="top">
     <td width="50">Prodi</td>
     <td align="center" width="10">:</td>
	 <td width="310" class="mark"><?= mUnit::getNamaUnit($conn,$r_jurusan) ?></td>
	 </tr>
	 <tr valign ="top">
	 <td>Tanggal Ujian</td>
     <td align="center" width="10">:</td>
	 <td width="310" class="mark"><?= CStr::formatDateInd(CStr::formatDate($tglawal))?> s/d <?=CStr::formatDateInd(CStr::formatDate($tglakhir)) ?></td>
   </tr></table>
   <br>
<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">

<tr bgcolor = "green">
	<th style="color:#FFFFFF">No</th>
	<th style="color:#FFFFFF">NIM</th>
	<th style="color:#FFFFFF">Nama</th>
	<th style="color:#FFFFFF">Judul Skripsi</th>
	<th style="color:#FFFFFF">Ruang</th>
	<th style="color:#FFFFFF">Tanggal Ujian</th>
	<th style="color:#FFFFFF">Waktu Mulai</th>
	<th style="color:#FFFFFF">Waktu Selesai</th>
</tr>
		
<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	$i++;
	?>
	
	<tr>
	<td><?=$i;?></td>
	<td><?=$row['nim']?></td>
	<td><?=$row['nama']?></td>
	<td><?=$row['judulta']?></td>
	<td><?=$row['koderuang']?></td>
	<td><?=CStr::formatDateInd($row['tglujian'])?></td>
	<td><? 
		if(strlen($row['waktumulai'])==1)
		{
			$waktu=str_pad($row['waktumulai'], 2, "0", STR_PAD_LEFT);
			$waktu.=".00";
		}
		elseif(strlen($row['waktumulai'])==2)
		{
			$waktu=$row['waktumulai'].".00";
		}
		elseif(strlen($row['waktumulai'])==3) 
		{
		$waktu=str_pad((substr($row['waktumulai'],0,1)), 2, "0", STR_PAD_LEFT);
		$waktu.=".00";
		}else
		{
		$waktu=substr($row['waktumulai'],0,2).".".substr($row['waktumulai'],2,2);
		}
		echo $waktu;?>
		</td>
	<td><?if(strlen($row['waktuselesai'])==1)
		{
			$waktu=str_pad($row['waktuselesai'], 2, "0", STR_PAD_LEFT);
			$waktu.=".00";
		}
		elseif(strlen($row['waktuselesai'])==2)
		{
			$waktu=$row['waktuselesai'].".00";
		}
		elseif(strlen($row['waktuselesai'])==3) 
		{
		$waktu=str_pad((substr($row['waktuselesai'],0,1)), 2, "0", STR_PAD_LEFT);
		$waktu.=".00";
		}else
		{
		$waktu=substr($row['waktuselesai'],0,2).".".substr($row['waktuselesai'],2,2);
		}
		echo $waktu;?></td>
	</tr>
	<? }?>
	</table>
	<table>
	<tr>
		<td width="750">&nbsp;</td>
		<td class="mark"><?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
    </table>
 </body></html>