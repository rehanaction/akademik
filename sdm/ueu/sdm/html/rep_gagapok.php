<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_periode = CStr::removeSpecial($_REQUEST['periodetarif']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_file = 'tarifgapok_'.$r_periode;
	$p_model = 'mGaji';
	$p_window = 'Daftar Tarif Gaji Pokok';
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
		
	//mendapatkan data gaji pokok
    $a_laporan = $p_model::repLapGapok($conn,$r_periode);
	$a_data = $a_laporan['data'];
	$namaperiode = $a_laporan['namaperiode'];
	$a_pkt = $a_laporan['pangkat'];
	$a_gol = $a_laporan['gol'];
	$a_ngol = $a_laporan['jmlgol'];
	$a_mk = $a_laporan['mk'];
	
	$p_title = 'Daftar Tarif Gaji Pokok<br />';	
	$p_title .= 'Periode '.$namaperiode;
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
	<div align="center">
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<thead>
				<tr>
					<th align="center" rowspan="2">TAHUN</th>
					<?foreach($a_gol as $gol){?>
					<th align="center" colspan="<?= $a_ngol[$gol]?>">GOLONGAN <?= strtoupper($gol)?></th>
					<?}?>
				</tr>
				<tr>
					<?foreach($a_pkt as $pkt){?>
					<th align="center"><?= strtoupper($pkt)?></th>
					<?}?>
				</tr>
			</thead>
			<tbody>
			<? 	
				foreach($a_mk as $mk){
			?>
				<tr>
					<td align="center"><?= $mk?></td>
					<?foreach($a_pkt as $pkt => $vpkt){?>
					<td align="right"><?= !empty($a_data[$pkt][$mk]) ? CStr::formatNumber($a_data[$pkt][$mk]) : ''?>&nbsp;</td>
					<?}?>
				</tr>
				<?}?>
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