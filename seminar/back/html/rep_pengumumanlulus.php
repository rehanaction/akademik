<?
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        
require_once($conf['model_dir'].'m_pendaftar.php'); 
require_once($conf['model_dir'].'m_smu.php');
require_once($conf['model_dir'].'m_combo.php');
//parameter
$periode    = CStr::removeSpecial($_REQUEST['periode']);
$jalur      = CStr::removeSpecial($_REQUEST['jalur']);
$pilihanditerima  = CStr::removeSpecial($_REQUEST['pilihanditerima']);
$r_format = CStr::removeSpecial($_REQUEST['format']);
$list_prodi=mCombo::jurusan($conn);
//model
$p_model='mPendaftar';
$p_title ='LAPORAN DATA PENDAFTAR YANG DITERIMA '.(!empty($pilihanditerima)?'DI '.strtoupper($list_prodi[$pilihanditerima]):'');
$p_tbwidth = 1200;
$rs_sma = mSmu::getSmu();
	$list_smu = array();
	$list_alamatsmu = array();
	$list_tlpsmu = array();
	while($row = $rs_sma->FetchRow()){
		$list_smu[$row['idsmu']] = $row['namasmu'];
		$list_alamatsmu[$row['idsmu']] = $row['alamatsmu'];
		$list_tlpsmu[$row['idsmu']] = $row['telpsmu'];
	}


$pendaftar=$p_model::getReportPendaftar($conn, $periode,$jalur,'',$pilihanditerima);
$p_namafile='kelulusan_'.$periode.'_'.$jalur.'_'.$pilihanditerima;
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
				<td rowspan="2"><strong>Asal SMU</strong></td>
				<td rowspan="2"><strong>Periode Daftar</strong></td>
				<td rowspan="2"><strong>Jalur Penerimaan.</strong></td>
				<td rowspan="2"><strong>Gel.</strong></td>
				<td rowspan="2"><strong>Diterima Di</strong></td>
				<td rowspan="2"><strong>Status Daftar Ulang</strong></td>
				
			</tr>
			
		</thead>
		<tbody>
			<?
		   
			$no=0;
			while($data = $pendaftar->FetchRow()){
					
				if(!empty($data['pilihanditerima'])){
					$no++;	
			?>
			<tr>
				<td><?=$no;?></td>
				<td><?=$data['nopendaftar']?></td>
				<td><?=$data['namalengkap'] ?></td>
				<td><?=$data['sex']=='L'?'Laki - Laki':'Perempuan' ?></td>
				<td><?=$data['namakota']?> / <?= !empty($data['tgllahir'])?date('d-m-Y',strtotime($data['tgllahir'])):''?></td>
				<td><?=$list_smu[$data['asalsmu']] ?></td>
				<td><?=$data['periodedaftar'] ?></td>
				<td><?=$data['jalurpenerimaan'] ?></td>
				<td align="center"><?=$data['idgelombang'] ?></td>
				<td><?=$list_prodi[$data['pilihanditerima']] ?></td>
				<td><?=$data['isdaftarulang']==-1?'Sudah':'Belum' ?></td>
			</tr>
		</tbody>
			<?php } } ?>
	</table>
	</div>
	</center>
	</body>
</html>
