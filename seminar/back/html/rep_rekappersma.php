<?
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        
require_once($conf['model_dir'].'m_pendaftar.php'); 

//parameter
$periode    = CStr::removeSpecial($_REQUEST['periode']);
$jalur      = CStr::removeSpecial($_REQUEST['jalur']);

 $r_format = CStr::removeSpecial($_REQUEST['format']);

//model
$p_model='mPendaftar';
$p_title ='LAPORAN REKAP PENDAFTAR PER SMA';
$p_tbwidth = 700;

		
$pendaftar=$p_model::rekapPerSma($conn, $periode,$jalur);


$p_namafile='statistik_'.$periode.'_'.$jalur.'_'.$jurusan;
Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
<html>
	<head>
			<title><?=$p_title?></title>   
			<link rel="icon" type="image/x-icon" href="image/favicon.png">
			<link href="style/style.css" rel="stylesheet" type="text/css">
		<style>
		a { color:black;text-decoration:none;cursor:pointer}
		</style>
	</head>
	<body style="background:white" onLoad="window.print();">
		<form name="pageform" id="pageform" method="post">
			<center>
			<div style="border: solid; border-width: thin; border-color: #c5c5c5; width:<?=$p_tbwidth ?>px;" >
			
			<table width="<?=$p_tbwidth ?>px">
			<tr>
			<td align="center" width="100"><img src="images/logo.jpg" height="70"></td>
			<td align="left">
				   
				<span style="font-size: 1;">KEMENTRIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
								<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL SURABAYA</span><br>
								<span style="font-size: 1;">Jalan Arjuna Utara No.9, Kebon jeruk-Jakarta Barat 11510</span><br>
								<span style="font-size: 1;">021-5674223 (hunting) 021-5682510 (direct) Fax: 021-5674248 Website:www.esaunggul.ac.id, email:info@esaunggul.ac.id</span>

			</td>
		</tr>
	</table>
	<hr>
	<center>
	  <strong>
		 <?=$p_title?><br>
		  <?=!empty($jalur)?'PROGRAM '.strtoupper($jalur):''?><br>
		  
		<?=!empty($periode)?'PERIODE '.$periode.'/'.($periode+1):''?>
	</strong>
	<br><br>
	<table width="100%" border=1 cellspacing=0 cellpadding="4">
		<thead>
			<tr align="center">
				<td ><strong>No</strong></td>
				<td ><strong>Nama SMA</strong></td>
				<td ><strong>Jumlah Pendaftar</strong></td>
				<td ><strong>Jumlah Daftarulang</strong></td>
				
			</tr>
		</thead>
		<tbody>
			<?
				$no=0;
				$tot_daftar=0;$tot_dafulang=0;
				foreach($pendaftar as $row){
					$no++;
					$tot_daftar+=$row['jumdaftar'];
					$tot_dafulang+=$row['jumdaftarulang'];
					$key=$periode.'|'.$jalur.'|'.$row['idsmu'];
			?>
			
			<tr align="center">
				<td><?=$no?></td>
				<td align="left"><?=$row['namasmu'];?></td>
				<td align="center"><a id="<?=$key?>" href="#" onclick="goPendaftar(this.id,'<?= Route::navAddress('rep_pendaftar') ?>')"><?=$row['jumdaftar'];?></a></td>
				<td align="center"><a id="<?=$key?>" href="#" onclick="goPendaftar(this.id,'<?= Route::navAddress('rep_daftarulang') ?>')"><?=$row['jumdaftarulang'];?></a></td>
			</tr>
			<?php } ?>
			<tr>
				<th colspan="2">Jumlah</th>
				<th><?=$tot_daftar?></th>
				<th><?=$tot_dafulang?></th>
			</tr>
			</tbody>
	</table>
	</div>
	</center>
		<input type="hidden" name="repkey" id="repkey">
	</form>
	</body>
</html>
 <script>
 function goPendaftar(key, url){
	var action = document.getElementById("pageform").action;
	var target = document.getElementById("pageform").target;
	
	document.getElementById("repkey").value = key;
	document.getElementById("pageform").action = url;
	document.getElementById("pageform").target = "_blank";
	
	document.getElementById("pageform").submit();
	
	document.getElementById("pageform").action = action;
	document.getElementById("pageform").target = target;
}
 </script>
