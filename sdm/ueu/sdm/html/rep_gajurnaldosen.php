<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_idpegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_periodegaji = CStr::removeSpecial($_REQUEST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 13;
	$p_file = 'rekapjurnaldosen'.(!empty($r_kodeunit) ? '_'.$r_kodeunit : '');
	$p_model = 'mGaji';
	$p_window = 'Rekapitulasi Jurnal Dosen';
	
	// header
	switch($r_format) {
		case 'doc';
			header("Content-Type: application/msword");
			header('Content-Disposition: attachment; filename="'.$p_file.'.doc"');
			break;
		case 'xls' :
			header("Content-Type: application/msexcel");
			header('Content-Disposition: attachment; filename="'.$p_file.'.xls"');
			break;
		default : header("Content-Type: text/html");
	}
		
    $a_data = $p_model::getRekapJurnalDosen($conn,$r_kodeunit,$r_periodegaji,$r_idpegawai);
	
	$rs = $a_data['list'];
	$a_datadosen = $a_data['data'];
	$a_jurnaldosen = $a_data['detail'];
	$periodegaji = $a_data['periodegaji'];
	
	print_r($a_datapeg);
	
	$p_title = 'Rekapitulasi Jurnal Dosen <br />';
	if(!empty($a_data['namaunit']))
		$p_title .= 'Unit '.$a_data['namaunit'].'<br />';
	$p_title .= 'Periode '.$periodegaji;
?>

<html>
<head>
<title><?= $p_window; ?></title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<style>
table { border-collapse:collapse }
div,td,th {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
}
td,th { border:1px solic black }
</style>
</head>
<body>
<?
if (count($a_datadosen) > 0) {
foreach($a_datadosen as $row):?>
<div align="center" style="page-break-after:always">
	<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0" bgcolor = "white">
			<tr>
				<td width="150">NIK</td>
				<td width="5">:</td>
				<td><b><?= $row['nik']?></b></td>
			</tr>
			<tr>
				<td width="150">NAMA PEGAWAI</td>
				<td width="5">:</td>
				<td><b><?= $row['namalengkap'] ?></b></td>
			</tr>
			<tr>
				<td width="150">UNIT KERJA</td>
				<td width="5">:</td>
				<td><b><?= $row['namaunit']; ?></b></td>
			</tr>
		</table>
		<br>
		
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">			
			<tr bgcolor = "gray">
				<th><b  style = "color:#FFFFFF">TGL. MENGAJAR</b></th>
				<th><b  style = "color:#FFFFFF">MATA KULIAH</b></th>
				<th><b  style = "color:#FFFFFF">JENIS PERTEMUAN</b></th>
				<th><b  style = "color:#FFFFFF">WAKTU MENGAJAR</b></th>
				<th><b  style = "color:#FFFFFF">JUMLAH JAM</b></th>
			</tr>
			<?php 
			$totaljam = 0;
			if (count($a_jurnaldosen[$row['idpegawai']]) > 0) {
				foreach($a_jurnaldosen[$row['idpegawai']] as $r):
			?>
					<tr>
						<td><?= CStr::formatDateInd($r['tglkuliah']); ?></td>
						<td><?= $r['namamk']; ?></td>
						<td><?= $r['jeniskul']; ?></td>
						<td align="center"><?= CStr::formatJam($r['waktumulai']).' - '.CStr::formatJam($r['waktuselesai']); ?></td>
						<td align="center"><?= $r['jmljam']; ?></td>
					</tr>
			<?php 
				$totaljam += $r['jmljam'];
				endforeach;?>
				<tr>
					<td colspan="4" align="center"><b>Total Jam</b></td>
					<td align="center"><?= $totaljam ?></td>
				</tr>
			<?	}else{
			?>	
			<tr>
				<td align="center" colspan="11"><strong>Data tidak ditemukan</strong></td>
			</tr>	
			<?}?>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php');?>
</div>
 <?php endforeach;
 }?>
</body>
 </html>
 
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
 
