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
	$p_tbwidth = 1300;
	$p_col = 12;
	$p_file = 'rekapkandidat_'.$r_kodeunit;
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
	
    $a_data = $p_model::getLapKandidat($conn,$r_kodeunit, $r_tglmulai, $r_tglselesai);
	
	$rs = $a_data['list'];
	$a_unit = $a_data['a_unit'];
	
	$p_title = 'Rekapitulasi Daftar Pelamar <br />
				Unit '.$a_data['namaunit'];
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
				<th rowspan="2"><b style = "color:#FFFFFF">Nama</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">Tempat, Tgl. Lahir</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">Alamat</b></th>
				<th colspan="3"><b style = "color:#FFFFFF">Kontak</b></th>
				<th colspan="3"><b style = "color:#FFFFFF">Pendidikan</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">Ambil Posisi</b></th>
				<th rowspan="2"><b style = "color:#FFFFFF">Unit Kerja</b></th>
			</tr>
			<tr bgcolor = "gray">
				<th><b style = "color:#FFFFFF">No. Telp</b></th>
				<th><b style = "color:#FFFFFF">No. HP</b></th>
				<th><b style = "color:#FFFFFF">Email</b></th>
				<th><b style = "color:#FFFFFF">Jurusan</b></th>
				<th><b style = "color:#FFFFFF">Universitas</b></th>
				<th><b style = "color:#FFFFFF">IPK</b></th>
			</tr>
			<? $i=0;
				if (count($rs) > 0){
					foreach($rs as $row){
						$i++;
			?>
			<tr>
				<td align="right"><?= $i; ?></td>
				<td valign="top"><?= $row['namalengkap']; ?></td>
				<td valign="top"><?= $row['tmplahir'].', '.CStr::formatDateInd($row['tgllahir']); ?></td>
				<td valign="top"><?= $row['alamat']; ?></td>
				<td valign="top"><?= $row['telp']; ?></td>
				<td valign="top"><?= $row['hp']; ?></td>
				<td valign="top"><?= $row['email']; ?></td>
				<td valign="top"><?= $row['namapendidikan'].', '.$row['jurusan']; ?></td>
				<td valign="top"><?= $row['namainstitusi']; ?></td>
				<td valign="top"><?= $row['ipk']; ?></td>
				<td valign="top"><?= $row['namaposisi']; ?></td>
				<td valign="top">
				<?php
					if(count($a_unit[$row['idrekrutmen']]) > 0){
						$i=0;
						foreach ($a_unit[$row['idrekrutmen']] as $idunti => $namaunit) {
							if($i>0)
								echo '<br>';
							echo '- '.$namaunit;
							$i++;
						}
					}
				?>
				</td>
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