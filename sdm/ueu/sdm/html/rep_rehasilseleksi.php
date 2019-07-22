<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_POST['unit']);
	$r_tglmulai = CStr::formatDate($_POST['tglmulai']);
	$r_tglselesai = CStr::formatDate($_POST['tglselesai']);
	$r_jenis = CStr::removeSpecial($_POST['jenis']);
	$r_format = CStr::removeSpecial($_POST['format']);
	
	require_once(Route::getModelPath('rekrutmen'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 9;
	$p_file = 'rekapseleksi_'.$r_kodeunit;
	$p_model = 'mRekrutmen';
	$p_window = 'Rekapitulasi Hasil Seleksi Penerimaan Pegawai';
	
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
	
	$a_jenisrekrutmen = $p_model::jenisRekrutmen();
	
    $a_data = $p_model::getLapHasilSeleksi($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai, $r_jenis);
	
	$rs = $a_data['list'];
	
	$p_title = 'Rekapitulasi Hasil Seleksi Penerimaan Pegawai <br />
				Unit '.$a_data['namaunit'].'<br />
				Jenis Rekrutmen '.$a_jenisrekrutmen[$r_jenis].'<br />
				Periode '.CStr::formatDateInd($r_tglmulai).' s/d '.CStr::formatDateInd($r_tglselesai);
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
				<th style = "color:#FFFFFF">No</th>
				<th style = "color:#FFFFFF">Nama</th>
				<th style = "color:#FFFFFF">Unit</th>
				<th style = "color:#FFFFFF">Jenis Rekrutmen</th>
				<th style = "color:#FFFFFF">Jenis Pegawai</th>
				<th style = "color:#FFFFFF">Posisi</th>
			</tr>
			<? $i=0;
				if (count($rs) > 0){
					foreach($rs as $row){
						$i++;
			?>
			<tr>
				<td align="right"><?= $i; ?></td>
				<td><?= $row['namalengkap']; ?></td>
				<td><?= $row['namaunit']; ?></td>
				<td><?= $a_jenisrekrutmen[$row['jenisrekrutmen']]; ?></td>
				<td><?= $row['jenispegawai']; ?></td>
				<td><?= $row['posisikaryawan']; ?></td>
			</tr>
			<? }}
				else{
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