<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('riwayat'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_col = 15;
	$p_file = 'riwayatpendidikandosen_'.$r_kodeunit;
	$p_model = 'mRiwayat';
	$p_window = 'Daftar Riwayat Pendidikan Dosen';
	
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
	
    $a_data = $p_model::repRiwayatPendidikanDosen($conn,$r_kodeunit);
	
	$rs = $a_data['list'];
	$data = $a_data['data'];
	
	$p_title = 'Daftar Riwayat Pendidikan Dosen<br />
				Homebase '.$a_data['namaunit'];
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
				<th rowspan="2"><b style = "color:#FFFFFF">No</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">No. Dosen</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">NIDN</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">Nama</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">Jenis Pegawai</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">Homebase</b></th>
				<th colspan="3"><b style = "color:#FFFFFF">Sarjana S1</b></th>
				<th colspan="3"><b style = "color:#FFFFFF">Sarjana S2</b></th>
				<th colspan="3"><b style = "color:#FFFFFF">Sarjana S3</b></th>
			</tr>
			<tr bgcolor = "gray">
				<th><b style = "color:#FFFFFF">Fakultas</b></th>
				<th><b style = "color:#FFFFFF">Prodi/ Jurusan</b></th>
				<th><b style = "color:#FFFFFF">Gelar</b></th>
				<th><b style = "color:#FFFFFF">Fakultas</b></th>
				<th><b style = "color:#FFFFFF">Prodi/ Jurusan</b></th>
				<th><b style = "color:#FFFFFF">Gelar</b></th>
				<th><b style = "color:#FFFFFF">Fakultas</b></th>
				<th><b style = "color:#FFFFFF">Prodi/ Jurusan</b></th>
				<th><b style = "color:#FFFFFF">Gelar</b></th>
			</tr>
			
			<? $i=0;
				while ($row = $rs->FetchRow()){ $i++;
			?>
			
			<tr>
				<td><?= $i; ?></td>
				<td><?= $row['nodosen']; ?></td>
				<td><?= $row['nidn']; ?></td>
				<td><?= $row['namapegawai']; ?></td>
				<td><?= $row['jenispegawai']; ?></td>
				<td><?= $row['namaunit']; ?></td>
				<td><?= $data[$row['idpegawai']]['51']['F']; ?></td>				
				<td><?= $data[$row['idpegawai']]['51']['J']; ?></td>				
				<td><?= $data[$row['idpegawai']]['51']['G']; ?></td>
				<td><?= $data[$row['idpegawai']]['52']['F']; ?></td>				
				<td><?= $data[$row['idpegawai']]['52']['J']; ?></td>				
				<td><?= $data[$row['idpegawai']]['52']['G']; ?></td>
				<td><?= $data[$row['idpegawai']]['53']['F']; ?></td>				
				<td><?= $data[$row['idpegawai']]['53']['J']; ?></td>				
				<td><?= $data[$row['idpegawai']]['53']['G']; ?></td>				
			</tr>
			
			<? }
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
