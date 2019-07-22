<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_POST['unit']);
	$r_tglmulai = CStr::formatDate($_POST['tglmulai']);
	$r_tglselesai = CStr::formatDate($_POST['tglselesai']);
	$r_format = CStr::removeSpecial($_POST['format']);
	
	require_once(Route::getModelPath('rekrutmen'));
	require_once(Route::getUIPath('form'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_col = 9;
	$p_file = 'rekappelamar_'.$r_kodeunit;
	$p_model = 'mRekrutmen';
	$p_window = 'Rekapitulasi Daftar Pelamar';
	
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
	
    $a_data = $p_model::getLapPelamar($conn, $r_tglmulai, $r_tglselesai);
	
	$rs = $a_data['list'];
	
	$p_title = 'Rekapitulasi Daftar Pelamar <br />
				Tanggal '.CStr::formatDateInd($r_tglmulai).' s/d '.CStr::formatDateInd($r_tglselesai);
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
				<th style = "color:#FFFFFF">Foto</th>
				<th style = "color:#FFFFFF">Nama</th>
				<th style = "color:#FFFFFF">Tgl. Lahir</th>
				<th style = "color:#FFFFFF">Alamat</th>
				<th style = "color:#FFFFFF">Telp.</th>
				<th style = "color:#FFFFFF">Pendidikan</th>
				<th style = "color:#FFFFFF">Institusi</th>
				<th style = "color:#FFFFFF">Jurusan</th>
				<th style = "color:#FFFFFF">IPK</th>
			</tr>
			<? $i=0;
				if (count($rs) > 0){
					foreach($rs as $row){
						$i++;
			?>
			<tr>
				<td valign="top" align="right"><?= $i; ?></td>
				<td><?= uForm::getImageFotoRep($conn,$row['nopendaftar'],'fotopelamar') ?></td>
				<td valign="top"><?= $row['namalengkap']; ?></td>
				<td valign="top"><?= CStr::formatDateInd($row['tgllahir']); ?></td>
				<td valign="top"><?= $row['alamat']; ?></td>
				<td valign="top"><?= $row['telp']; ?></td>
				<td valign="top"><?= $row['namapendidikan']; ?></td>
				<td valign="top"><?= $row['namainstitusi']; ?></td>
				<td valign="top"><?= $row['jurusan']; ?></td>
				<td valign="top"><?= $row['ipk']; ?></td>
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