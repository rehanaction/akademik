<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_periode = CStr::removeSpecial($_REQUEST['periodetarif']);
	$r_tunjangan = CStr::removeSpecial($_REQUEST['tunjangan']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_file = 'tariftunjangan_'.$r_periode;
	$p_model = 'mGaji';
	$p_window = 'Daftar Tarif Tunjangan';
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
	$a_laporan = $p_model::repLapTarifTunjangan($conn,$r_periode,$r_tunjangan);
	$a_datastruktural = $a_laporan['datastruktural'];
	$a_data = $a_laporan['data'];
	$namaperiode = $a_laporan['namaperiode'];
	$namatunjangan = $a_laporan['namatunjangan'];
	$tunj = $p_model::infoTunjangan($conn,$r_tunjangan);
	$hubunganPeg = $p_model::getHubunganPeg($conn);
	$tunjPeg = $p_model::getInfoTunjanganPeg($conn,$r_tunjangan);
	$level = $p_model::getInfoLevel($conn);
	
	$p_title = 'Daftar Tarif '.$namatunjangan.'<br/>';	
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
					<th <?if(!empty($tunj['info2'])) echo 'rowspan="3"'; else ''?> style = "color:#FFFFFF">No</th>
					<th <?if(!empty($tunj['info2'])) echo 'rowspan="3"'; else ''?> style = "color:#FFFFFF"><?= $tunj['namatunjangan']?></th>
					<th <?if(!empty($tunj['info2'])) echo 'colspan="'.count($hubunganPeg).'"'; else ''?> style = "color:#FFFFFF">Tarif Tunjangan</th>
				<?if(!empty($tunj['info2'])){?>
					</tr>
					<tr bgcolor = "gray">
						<th colspan="<?= count($hubunganPeg)?>" style = "color:#FFFFFF">Hubungan Kerja</th>
					</tr>
					<tr bgcolor = "gray">
						<? if (count($hubunganPeg) > 0){
								foreach($hubunganPeg as $col => $key){
						?>
							<th style = "color:#FFFFFF"><?= $key ?></th>
						<? }} ?>
					</tr>
				<?}else{?>
				</tr>
				<?}?>
			</thead>
			<tbody>
				<? $no=0; 
					if (count($tunjPeg) > 0){
						foreach($tunjPeg as $row => $key){
						$no=$no+1;
				?>
				<tr>
					<td align="center"><?= $no ?></td>
					<td style="padding-left:<?= $level[$row]*10?>px"><?= $key?></td>
					<?if(!empty($tunj['info2'])){
						if (count($hubunganPeg) > 0){
							foreach($hubunganPeg as $col => $key){
						?>
							<td><?= !empty($a_datastruktural[$row][$col]) ? CStr::formatNumber($a_datastruktural[$row][$col]).'&nbsp;' : ''?></td>
						<?}}}else{?>
							<td><?= !empty($a_data[$row]) ? CStr::formatNumber($a_data[$row]).'&nbsp;' : ''?></td>
						<?}?>
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