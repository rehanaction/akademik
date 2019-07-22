<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tglmulai = CStr::formatDate($_REQUEST['tglmulai']);
	$r_tglselesai = CStr::formatDate($_REQUEST['tglselesai']);
	$r_jenissk = CStr::removeSpecial($_REQUEST['jenissk']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('riwayat'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_col = 11;
	$p_file = 'riwayatpangkat_'.$r_kodeunit;
	$p_model = 'mRiwayat';
	$p_window = 'Daftar Riwayat Pangkat Pegawai';
	
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
	
    $a_data = $p_model::repRiwayatPangkat($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_jenissk);
	$jns = $p_model::jenisPangkat();
	
	$rs = $a_data['list'];
	
	$p_title = 'Daftar Riwayat Pangkat Pegawai <br />
				Unit '.$a_data['namaunit'].'<br />
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
				<th><b style = "color:#FFFFFF">No</b></th>
				<th><b style = "color:#FFFFFF">NIP</b></th>
				<th><b style = "color:#FFFFFF">Nama Pegawai</b></th>
				<th><b style = "color:#FFFFFF">Unit Kerja</b></th>
				<th><b style = "color:#FFFFFF">Indeks Pegawai</b></th>
				<th><b style = "color:#FFFFFF">Nama Pangkat</th>
				<th><b style = "color:#FFFFFF">Tgl Efektif</b></th>
				<th><b style = "color:#FFFFFF">Jenis SK</b></th>				
				<th><b style = "color:#FFFFFF">Jenis Pangkat</b></th>
				<th><b style = "color:#FFFFFF">Masa Kerja</b></th>
				<th><b style = "color:#FFFFFF">Jabatan Akademik</b></th>
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
				<td><?= $row['namagolongan']; ?></td>
				<td align="center"><?= CStr::formatDateInd($row['tmtpangkat']); ?></td>
				<td><?= $row['namajenissk']; ?></td>
				<td><?= $jns[$row['jenispangkat']]; ?></td>
				<td><?= $row['masakerja']; ?></td>
				<td><?= $row['jabatanfungsional']; ?></td>
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
