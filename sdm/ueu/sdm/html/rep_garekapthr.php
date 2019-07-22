<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_periodethr = CStr::removeSpecial($_REQUEST['periodethr']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	$r_jenis = $_POST['jenis'];
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_file = 'rekapthr'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Rekapitulasi THR';
	$p_col = 8;
	
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
	
	//bila diunduh bentuk doc atau excel, koma rupiah dihilangkan
	$dot = true;
	if($r_format == 'doc' or $r_format == 'xls')
		$dot = false;
	
	//mendapatkan data thr
    $a_laporan = $p_model::repRekapGajiTHR($conn,$r_periodethr,$r_kodeunit);
	$a_data = $a_laporan['data'];
	$prorata = $a_laporan['prorata'];
	$namaperiodethr = $a_laporan['namaperiodethr'];
	
	$p_title = 'Rekapitulasi Gaji THR Universitas Esa Unggul <br />';
	
	$p_title .= 'Periode '.$namaperiodethr;
?>
<html>
<head>
	<title><?= $p_window; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<style>
	div,td,th {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	}
	td,th { border:1px solic black }
	</style>
</head>
<body>
	<div align="center">
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" border="1" cellspacing="0" style="border-collapse:collapse">
			<thead>
				<tr>
					<th align="center">NO</th>
					<th align="center">NIP</th>
					<th align="center">NAMA</th>
					<th align="center" width="130">TGL. MASUK KERJA</th>
					<th align="center" width="130">MASA KERJA</th>
					<th align="center">PRORATA (%)</th>
					<th align="center">GAJI</th>
					<th align="center">PPh. 21</th>
				</tr>
			</thead>
			<? $totalgaji=0;$totalpph=0;
				if (count($a_data) >0) {
				$i=0;
				foreach($a_data as $row){
					$totalgaji += $row['gajidibulatkan'];
					$totalpph += $row['pph'];
			?>
			<tbody>
				<tr>
					<td align="right"><?= ++$i; ?></td>
					<td><?= $row['nik']; ?></td>
					<td><?= $row['namapegawai']; ?></td>
					<td><?= CStr::formatDateInd($row['tmk']); ?></td>
					<td align="center"><?= $row['masakerja']; ?></td>
					<td align="center"><?= empty($prorata[$row['idpegawai']]) ? '': round(($prorata[$row['idpegawai']]*100),1) ?></td>
					<td align="right"><?= CStr::formatNumber($row['gajidibulatkan'],0,$dot) ?></td>
					<td align="right"><?= CStr::formatNumber($row['pph'],0,$dot) ?></td>
				</tr>
				<? }}else{ ?>
				<tr>
					<td colspan="<?= $p_col?>" align="center">Data tidak ditemukan</td>
				</tr>
				<? } ?>
				<tr>
					<td  align="center" colspan="<?= $p_col-2?>" align="right"><strong>Total</strong></td>
					<td align="right"><strong><?= CStr::formatNumber($totalgaji,0,$dot); ?><strong></td>
					<td align="right"><strong><?= CStr::formatNumber($totalpph,0,$dot); ?><strong></td>
				</tr>
			</tbody>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>