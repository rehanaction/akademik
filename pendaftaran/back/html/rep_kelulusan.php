<?
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        
require_once($conf['model_dir'].'m_pendaftar.php'); 
require_once($conf['model_dir'].'m_smu.php');
require_once($conf['model_dir'].'m_combo.php');
//parameter
$periode    = CStr::removeSpecial($_REQUEST['periode']);
$jalur      = CStr::removeSpecial($_REQUEST['jalur']);
$pilihan  = CStr::removeSpecial($_REQUEST['pilihan1']);
$tahapujian  = CStr::removeSpecial($_REQUEST['tahapujian']);
$r_format = CStr::removeSpecial($_REQUEST['format']);
$tahap = mCombo::getTahapUjian();

//model
$p_model='mPendaftar';
$p_title ='LAPORAN DATA PENDAFTAR YANG '.strtoupper($tahap[$tahapujian]);
$p_tbwidth = 1200;


$list_prodi=mCombo::jurusan($conn);
$pendaftar=$p_model::getReportLulus($conn, $periode,$jalur,$pilihan,$tahapujian);

$p_namafile='kelulusan_'.$periode.'_'.$jalur.'_'.$tahap[$tahapujian];
Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
<html>
	<head>
			<title><?=$p_title?></title>   
			<link rel="icon" type="image/x-icon" href="image/favicon.png">
			<link href="style/style.css" rel="stylesheet" type="text/css">
			
	</head>
	<body style="background:white" onLoad="window.print();">
  
			<center>
			<div style="border: solid; border-width: thin; border-color: #c5c5c5; width:<?=$p_tbwidth ?>px;" >
			
			<table width="<?=$p_tbwidth ?>px">
			<tr>
			<td align="center" width="100"><img src="images/logo.jpg" height="70"></td>
			<td align="left">
				   
				<span style="font-size: 1;">KEMENTRIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
								<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL JAKARTA</span><br>
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
		  <?=!empty($pilihan)?strtoupper($list_prodi[$pilihan]):''?>
		  <br>
		<?=!empty($periode)?'PERIODE '.$periode.'/'.($periode+1):''?>
			
	</strong>      
	</center> 
	<br>
	<table width="100%" border=1 cellspacing=0 cellpadding="4">
		<thead>
			<tr align="center">
				<td rowspan="2"><strong>No</strong></td>
				<td rowspan="2"><strong>No Pendaftar</strong></td>
				<td rowspan="2"><strong>Nama</strong></td>
				<td rowspan="2"><strong>Jenis kelamin</strong></td>
				<td rowspan="2"><strong>Tempat / Tgl Lahir</strong></td>
				<td rowspan="2"><strong>Periode Daftar</strong></td>
				<td colspan="3"><strong>Prodi</strong></td>
			</tr>
			<tr>
				<td><strong>Pilihan 1</strong></td>
				<td><strong>Pilihan 2</strong></td>
				<td><strong>Pilihan 3</strong></td>
			</tr>
		</thead>
		<tbody>
			<?
		   
			$no=0;
			while($data = $pendaftar->FetchRow()){
					$no++;
					
					
			?>
			<tr>
				<td><?=$no;?></td>
				<td><?=$data['nopendaftar']?></td>
				<td><?=$data['namalengkap'] ?></td>
				<td><?=$data['sex']=='L'?'Laki - Laki':'Perempuan' ?></td>
				<td><?=$data['namakota']?> / <?= !empty($data['tgllahir'])?date('d-m-Y',strtotime($data['tgllahir'])):''?></td>
				<td><?=$data['periodedaftar'] ?></td>
				<td><?=$list_prodi[$data['pilihan1']] ?></td>
				<td><?=$list_prodi[$data['pilihan2']] ?></td>
				<td><?=$list_prodi[$data['pilihan3']] ?></td>
			</tr>
		</tbody>
			<?php } ?>
	</table>
	</div>
	</center>
	</body>
</html>
