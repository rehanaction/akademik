<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_unit = CStr::removeSpecial($_POST['unit']);
	$r_periode = CStr::removeSpecial($_POST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('pa'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 8;
	$p_file = 'statistikpenilaiankinerja_'.$r_unit;
	$p_model = 'mPa';
	$p_window = 'Statistik Penilaian Kinerja';
	
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
	
    $a_laporan = $p_model::repStatistikPA($conn,$r_unit,$r_periode);
		
	$a_data = $a_laporan['list'];
	$a_kategori = $a_laporan['kategori'];
	$namaunit = $a_laporan['namaunit'];
	$a_sts = $a_laporan['sts'];
	
	$p_title = 'Statistik Penilaian Kinerja<br />Unit '.$namaunit.'<br>'.$a_laporan['periode'];;
?>
<html>
<head>
<title><?= $p_window; ?></title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<link rel="icon" type="image/x-icon" href="images/favicon.png">
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
		<? require_once($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse 1px">
			<tr bgcolor = "gray">
				<th rowspan="2" style = "color:#FFFFFF">No</th>
				<th rowspan="2" style = "color:#FFFFFF">Nama Unit</th>
				<th colspan="<?= count($a_kategori); ?>" style = "color:#FFFFFF">Kategori Nilai</th>
				<th rowspan="2" style = "color:#FFFFFF">Jumlah</th>
			</tr>
			<tr bgcolor = "gray">
				<? if (count($a_kategori) > 0){
						foreach($a_kategori as $col){
				?>
				<th style = "color:#FFFFFF"><?= $col['kategori']; ?></th>
				<? }} ?>
			</tr>
			<? if (count($a_data) > 0){ 
					$i =0 ;
					$a_totalgol = array();$total=0;
					foreach($a_data as $row){
						$i++;
			?>
			<tr>
				<td align="right"><?= $i; ?>.</td>
				<td width="500px"<?= 'style="padding-left:'.(($row['level']*15)+4).'px"'?>><?= $row['namaunit'] ?></td>
				<? if (count($a_kategori) > 0){
						foreach($a_kategori as $col){
							$a_ststotal[$row['idunit']] += $a_sts[$row['idunit']][$col['kodekategori']];
							$a_totalgol[$col['kodekategori']] += $a_sts[$row['idunit']][$col['kodekategori']];
				?>
				<td align="center"><?= $a_sts[$row['idunit']][$col['kodekategori']]; ?></td>
				<? }} ?>
				<td align="right"><?= $a_ststotal[$row['idunit']]; ?></td>
			</tr>
			<? } ?>
			<tr>
				<td colspan="2" align="center"><strong>Jumlah</strong></td>
				<? if (count($a_kategori) > 0){
						foreach($a_kategori as $col){
							$total += $a_totalgol[$col['kodekategori']];
				?>
				<td align="right"><?= $a_totalgol[$col['kodekategori']]; ?></td>
				<? }} ?>
				<td align="right"><?= $total; ?></td>
			</tr>
			<? }else{ ?>
			<tr>
				<td align="center" colspan="<?= count($a_kategori)+3; ?>">Data tidak ditemukan</td>
			</tr>
			<? } ?>
		</table>
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>