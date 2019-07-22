<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tglmulai = CStr::formatDate($_POST['tglmulai']);
	$r_tglselesai = CStr::formatDate($_POST['tglselesai']);
	$r_jenispeg = $_REQUEST['jenispeg'];
	$jenispeg = implode("','",$r_jenispeg);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('presensi'));
	
	// definisi variable halaman	
	$p_tbwidth = 1200;
	$p_col = 11;
	$p_file = 'rekapitulasilembur_'.$r_kodeunit;
	$p_model = 'mPresensi';
	$p_window = 'Rekapitulasi Lembur Karyawan';
	
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
	
    $a_data = $p_model::repRekapLembur($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$jenispeg);
	
	$a_lembur = $a_data['list'];
	
	$p_title = 'Rekapitulasi Lembur Karyawan<br />
				Unit '.$a_data['namaunit'].'<br />
				Periode '.$_POST['tglmulai'].' s/d '.$_POST['tglselesai'];
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
		<? require_once($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<tr bgcolor = "gray">
				<th><b style = "color:#FFFFFF">No</b></th>
				<th><b style = "color:#FFFFFF">NIP</b></th>
				<th><b style = "color:#FFFFFF">Nama Pegawai</b></th>
				<th><b style = "color:#FFFFFF">Tanggal Penugasan</b></th>
				<th><b style = "color:#FFFFFF">Tanggal Lembur</b></th>
				<th><b style = "color:#FFFFFF">Status</b></th>
				<th width="100px"><b style = "color:#FFFFFF">Jam Lembur</b></th>
				<th><b style = "color:#FFFFFF">Total Jam</b></th>
				<th><b style = "color:#FFFFFF">Pejabat Atasan</b></th>
				<th><b style = "color:#FFFFFF">Unit Lembur</b></th>
				<th><b style = "color:#FFFFFF">Lokasi</b></th>
			</tr>
			
			<? $i=0;
				foreach ($a_lembur as $row) {
					$i++;
					if($tempunit != $row['idunit']){
						$no=1;
			?>			
			<tr>
				<td colspan="<?= $p_col; ?>" align="center"><?= $row['namaunit']?></td>
			</tr>
			<? 	
					}
					if($temppeg != $row['idpegawai']){
			?>
			<tr>
				<td rowspan="<?= $a_data['rowspan'][$row['idpegawai']]?>" valign="top" align="center"><?= $no++; ?></td>
				<td rowspan="<?= $a_data['rowspan'][$row['idpegawai']]?>" valign="top" align="center"><?= $row['nik']; ?></td>
				<td rowspan="<?= $a_data['rowspan'][$row['idpegawai']]?>" valign="top"><?= $row['namapegawai']; ?></td>
			<? 
					}else
						echo '<tr>';
			?>
				<td align="center"><?= CStr::formatDate($row['tglpenugasan']); ?></td>
				<td align="center"><?= CStr::formatDate($row['tgllembur']); ?></td>
				<td align="center"><?= $row['jenislembur']; ?></td>
				<td align="center"><?= CStr::formatJam($row['jamawal']).' - '.CStr::formatJam($row['jamakhir']); ?></td>
				<td align="right"><?= CStr::formatNumber($row['jmljam']); ?></td>
				<td><?= $row['pimpinan']; ?></td>
				<td><?= $row['namaunit']; ?></td>
				<td><?= $row['lokasi']; ?></td>
			</tr>			
			<? 
				$temppeg = $row['idpegawai'];
				$tempunit = $row['idunit'];
				}
				if ($i == 0){
			?>
			
			<tr>
				<td colspan="<?= $p_col; ?>" align="center">Data tidak ditemukan</td>
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
