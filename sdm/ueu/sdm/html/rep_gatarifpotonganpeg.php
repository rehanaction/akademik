<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_periode = CStr::removeSpecial($_REQUEST['periodetarif']);
	$r_potonganpeg = CStr::removeSpecial($_REQUEST['potongan']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_file = 'tarifhonor_'.$r_periode;
	$p_model = 'mGaji';
	$p_window = 'Daftar Tarif Potongan Pegawai';
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
		
	//mendapatkan data tunjangan
	$a_laporan = $p_model::repLapTarifPotonganPeg($conn,$r_periode,$r_potonganpeg);
	$a_data = $a_laporan['data'];
	$namaperiode = $a_laporan['namaperiode'];
	$namapotongan = $a_laporan['namapotongan'];
	$potongan = $a_laporan['infopotongan'];
	
	$p_title = 'Daftar Tarif '.$namapotongan.'<br/>';	
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
				<tr bgcolor = "gray">
					<th style = "color:#FFFFFF">No</th>
					<th style = "color:#FFFFFF">Jenis Potongan</th>
					<th style = "color:#FFFFFF">Nama Pegawai</th>
					<th style = "color:#FFFFFF">Jumlah Tarif</th>
				</tr>
			</thead>
			<tbody>
				<? $no=0; 
					if (count($potongan) > 0){
						foreach($potongan as $row => $key){
						$no=$no+1;
				?>
				<tr>
					<td align="center"><?= $no ?></td>
					<td><?= $namapotongan?></td>
					<td><?= $key?></td>
					<td><?= !empty($a_data[$row]) ? CStr::formatNumber($a_data[$row]).'&nbsp;' : ''?></td>
				</tr>
				<?}} ?>
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