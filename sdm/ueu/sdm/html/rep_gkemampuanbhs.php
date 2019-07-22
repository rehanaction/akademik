<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun1 = CStr::removeSpecial($_REQUEST['tahun1']);
	$r_tahun2 = CStr::removeSpecial($_REQUEST['tahun2']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('pengembangan'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_col = 10;
	$p_file = 'kemampuanbahasa_'.$r_kodeunit;
	$p_model = 'mPengembangan';
	$p_window = 'Daftar Kemampuan Bahasa Pegawai';
	
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
		
    $a_data = $p_model::repKemampuanBahasa($conn,$r_kodeunit,$r_tahun1,$r_tahun2);
	
	$rs = $a_data['list'];
	
	$p_title = 'Daftar Kemampuan Bahasa Pegawai <br />
				Unit '.$a_data['namaunit'].'<br />
				Periode '.$r_tahun1.' s/d '.$r_tahun2;
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
				<th><b style = "color:#FFFFFF">Unit Kerja</b></th>
				<th><b style = "color:#FFFFFF">Indeks Pegawai</b></th>
				<th><b style = "color:#FFFFFF">Tahun</b></th>		
				<th><b style = "color:#FFFFFF">Nama Lembaga</b></th>
				<th><b style = "color:#FFFFFF">Kemampuan Mendengar</b></th>
				<th><b style = "color:#FFFFFF">Kemampuan Bicara</b></th>
				<th><b style = "color:#FFFFFF">Kemampuan Menulis</b></th>
			</tr>
			
			<? $i=0;
				while ($row = $rs->FetchRow()){ $i++;
			?>
			
			<tr>
				<td><?= $i; ?></td>
				<td><?= $row['nik']; ?></td>
				<td><?= $row['namapegawai']; ?></td>
				<td><?= $row['namaunit']; ?></td>
				<td><?= $row['tipepeg']; ?></td>
				<td align="center"><?= $row['tahunkemampuan']; ?></td>
				<td><?= $row['namalembaga']; ?></td>
				<td align="center"><?= $row['kemampuandengar']; ?></td>
				<td align="center"><?= $row['kemampuanbicara']; ?></td>
				<td align="center"><?= $row['kemampuantulisan']; ?></td>
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