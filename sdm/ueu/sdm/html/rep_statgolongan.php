<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_unit = CStr::removeSpecial($_POST['unit']);
	$r_unithb = CStr::removeSpecial($_POST['unithb']);
	$r_jenis = $_POST['jenis'];
	$r_aktif = $_POST['aktif'];
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('statistik'));
	
	// definisi variable halaman	
	$p_tbwidth = 1100;
	$p_col = 8;
	$p_file = 'rekapstsgolongan_'.(!empty($r_unit) ? $r_unit : (!empty($r_unithb) ? $r_unithb : ''));
	$p_model = 'mStatistik';
	$p_window = 'Rekapitulasi Statistik Pangkat/ Golongan';
	
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
	
	$sqljenis = '';
	if(count($r_jenis)>0){
		$i_jenis = implode("','",$r_jenis);
		$sqljenis = "and p.idjenispegawai in ('$i_jenis') ";
	}

	$sqlaktif = '';
	if(count($r_aktif)>0){
		$i_aktif = implode("','",$r_aktif);
		$sqlaktif = "and p.idstatusaktif in ('$i_aktif') ";
	}
	
    $a_laporan = $p_model::getSLapGol($conn,$r_unit,$r_unithb,$sqljenis,$sqlaktif);
		
	$a_data = $a_laporan['list'];
	$a_pangkat = $a_laporan['pangkat'];
	$namaunit = $a_laporan['namaunit'];
	$a_sts = $a_laporan['sts'];
	
	$p_title = 'Rekapitulasi Statistik Pangkat/ Golongan<br />Unit '.$namaunit;
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
				<th rowspan="2"><b style = "color:#FFFFFF">No</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">Nama Unit</b></th>
				<th colspan="<?= count($a_pangkat); ?>"><b style = "color:#FFFFFF">Pangkat/Gol.</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">Jumlah</b></th>
			</tr>
			<tr bgcolor = "gray">
				<? if (count($a_pangkat) > 0){
						foreach($a_pangkat as $col){
				?>
				<th><b style = "color:#FFFFFF"><?= $col['golongan']; ?></b></th>
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
				<? if (count($a_pangkat) > 0){
						foreach($a_pangkat as $col){
							$a_ststotal[$row['idunit']] += $a_sts[$row['idunit']][$col['idpangkat']];
							$a_totalgol[$col['idpangkat']] += $a_sts[$row['idunit']][$col['idpangkat']];
				?>
				<td align="center"><?= $a_sts[$row['idunit']][$col['idpangkat']]; ?></td>
				<? }} ?>
				<td align="right"><?= $a_ststotal[$row['idunit']]; ?></td>
			</tr>
			<? } ?>
			<tr>
				<td colspan="2" align="center"><strong>Jumlah</strong></td>
				<? if (count($a_pangkat) > 0){
						foreach($a_pangkat as $col){
							$total += $a_totalgol[$col['idpangkat']];
				?>
				<td align="right"><?= $a_totalgol[$col['idpangkat']]; ?></td>
				<? }} ?>
				<td align="right"><?= $total; ?></td>
			</tr>
			<? }else{ ?>
			<tr>
				<td align="center" colspan="<?= count($a_pangkat)+3; ?>">Data tidak ditemukan</td>
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
