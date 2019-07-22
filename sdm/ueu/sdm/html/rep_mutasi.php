<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_format = CStr::removeSpecial($_REQUEST['riwayat']);
	
	require_once(Route::getModelPath('riwayat'));
	
	// definisi variable halaman	
	$p_tbwidth = 600;
	$p_col = 13;
	$p_file = 'suratmutasi_'.$r_key;
	$p_model = 'mRiwayat';
	$p_window = 'Surat Mutasi';
	
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
		
    $a_data = $p_model::repSuratMutasi($conn,$r_key);

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
		
		<strong>SURAT MUTASI PEGAWAI<br />
		<hr style="width:200px" />
		<?= $a_data['nosk']; ?>
		</strong>
		<br />
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<tr>
				<td bgcolor="#CCCCCC" colspan="2" ><strong>Data Pegawai</strong></td>
			</tr>
			<tr>
				<td width="100px">NIP</td>
				<td><?= $a_data['nik']; ?></td>
			</tr>	
			<tr>
				<td width="170px">Nama</td>
				<td><?= $a_data['namapegawai']; ?></td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC" colspan="2" ><strong>Data Mutasi</strong></td>
			</tr>
			<tr>
				<td valign="top">Tgl. Mutasi</td>
				<td valign="top"><?= CStr::formatDateInd($a_data['tmttugas']); ?></td>
			</tr>
			<tr>
				<td valign="top">No. SK</td>
				<td valign="top"><?= $a_data['nosk']; ?></td>
			</tr>
			<tr>
				<td valign="top">Tgl. SK</td>
				<td valign="top"><?= CStr::formatDateInd($a_data['tglsk']); ?></td>
			</tr>
			<tr>
				<td valign="top">No. Surat Tugas</td>
				<td valign="top"><?= $a_data['nosurattugas']; ?></td>
			</tr>
			<tr>
				<td valign="top">Tgl. Surat Tugas</td>
				<td valign="top"><?= CStr::formatDateInd($a_data['tglsurattugas']); ?></td>
			</tr>
			<tr>
				<td valign="top">Jenis Mutasi</td>
				<td valign="top"><?= $a_data['jenismutasi']; ?></td>
			</tr>
			<tr>
				<td>Unit Asal</td>
				<td><?= $a_data['asalunit']; ?></td>
			</tr>
			<tr>
				<td>Unit Tujuan</td>
				<td><?= $a_data['tujuanunit']; ?></td>
			</tr>
			<tr>
				<td>Jenis Pegawai Asal</td>
				<td><?= $a_data['jenispegasal']; ?></td>
			</tr>
			<tr>
				<td>Jenis Pegawai Tujuan</td>
				<td><?= $a_data['jenispegtujuan']; ?></td>
			</tr>
		</table>
		<br />
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>