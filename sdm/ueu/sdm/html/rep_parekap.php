<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_unit = CStr::removeSpecial($_POST['unit']);
	//$r_kategori = CStr::removeSpecial($_POST['kategori']);
	$r_periode = CStr::removeSpecial($_POST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('pa'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 8;
	$p_file = 'rekappa_'.$r_unit;
	$p_model = 'mPa';
	$p_window = 'Rekapitulasi Penilaian Kinerja';
	
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
	
    $a_laporan = $p_model::getLapPA($conn,$r_periode,$r_unit);
	
	$a_data = $a_laporan['list'];
	$a_jenispenilai = $a_laporan['jenispenilai'];
	$namaunit = $a_laporan['unit'];
	$namaperiode = $a_laporan['periode'];
	$a_nilaisubj = $a_laporan['nilaisubj'];
	
	$p_title = 'Rekapitulasi <br />
				Penilaian Kinerja Unit '.$namaunit.' <br />
				Periode '.$namaperiode;
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
		<? if (count($a_data) > 0){ ?>
		<br />
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse 1px">
			<tr bgcolor = "gray">
				<th rowspan="2" align="center">No</th>
				<th rowspan="2" align="center">Nama Karyawan</th>
				<th rowspan="2" align="center">Jabatan</th>
				<th colspan="<?= count($a_jenispenilai); ?>" align="center">Hasil Penilaian Rata-rata</th>
				<th colspan="<?= count($a_jenispenilai); ?>" align="center">Hasil Penilaian Rata-rata (%)</th>
				<th colspan="2" align="center">Subjektif</th>
				<th align="center">Objektif</th>
				<th colspan="2" align="center">Bobot Nilai</th>
				<th rowspan="2" align="center">Total Nilai</th>
			</tr>
			<tr bgcolor = "gray">
				<? if (count($a_jenispenilai) > 0){
						foreach($a_jenispenilai as $kode => $namajenis){
				?>
				<th align="center"><?= $namajenis; ?></th>
				<? }} ?>
				<? if (count($a_jenispenilai) > 0){
						foreach($a_jenispenilai as $kode => $namajenis){
				?>
				<th align="center"><?= $namajenis; ?></th>
				<? }} ?>
				<th align="center">360 (A)</th>
				<th align="center">Absen (B)</th>
				<th align="center">Pel (C)</th>
				<th align="center">Subj</th>
				<th align="center">Obj</th>
			</tr>
			<? if (count($a_data) > 0){ 
					$i =0 ;
					foreach($a_data as $row){
						$i++;
			?>
			<tr>
				<td align="right"><?= $i; ?>.</td>
				<td><?= $row['namalengkap']; ?></td>
				<td><?= $row['jabatanstruktural']; ?></td>
				<? if (count($a_jenispenilai) > 0){
						foreach($a_jenispenilai as $kode => $namajenis){
				?>
				<th align="center"><?= $a_nilaisubj['rata'][$row['idpegawai']][$kode]; ?></th>
				<? }} ?>
				<? if (count($a_jenispenilai) > 0){
						foreach($a_jenispenilai as $kode => $namajenis){
							$total[$row['idpegawai']] += (float)$a_nilaisubj['nilai'][$row['idpegawai']][$kode]; 
				?>
				<th align="center"><?= $a_nilaisubj['nilai'][$row['idpegawai']][$kode]; ?></th>
				<? }} ?>
				<td align="right"><?= $total[$row['idpegawai']] ?></td>
				<td align="right"><?= $row['nilaiob2']; ?></td>
				<td align="right"><?= $row['nilaiob1']; ?></td>
				<td align="right"><strong><?= $row['nilaisubyektif']; ?></strong> (<?= $row['bobotsubjektif']; ?>%)</td>
				<td align="right"><strong><?= $row['nilaiobyektif']; ?></strong> (<?= $row['bobotobjektif']; ?>%)</td>
				<td align="right"><?= $row['nilaiakhir']; ?></td>
			</tr>
			<? }} ?>
		</table>
		<? } ?>
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>