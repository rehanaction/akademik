<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('rekrutmen'));
	
	// definisi variable halaman	
	$p_tbwidth = 800;
	$p_col = 13;
	$p_file = 'formre_'.$r_key;
	$p_model = 'mRekrutmen';
	$p_window = 'Formulir Permohonan Tenaga Kerja';
	$p_title = 'Formulir Permohonan Tenaga Kerja';
	
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
			
    $a_data = $p_model::repFormPengajuan($conn,$r_key);
	
	$a_jenisrekrutmen = $p_model::jenisRekrutmen();
	$a_valid = SDM::getValid();
	$a_ketelitian = $p_model::aKetelitian();
	$a_kecepatan = $p_model::aKecepatan();
	$a_kecerdasan = $p_model::aKecerdasan();
	$a_sosial = $p_model::aSosial();
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
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr height="50px">
				<td colspan="4" align="center"><strong><?= $p_title; ?></strong></td>
			</tr>			
			<tr>
				<td colspan="3">DEPARTEMEN / UNIT / FAKULTAS / PRODI</td>
				<td>: <?= $a_data['namaunit']; ?></td>
			</tr>		
			<tr>
				<td width="30px"><strong>A.</strong></td>
				<td colspan="3"><strong>IDENTIFIKASI KEBUTUHAN</strong></td>
			</tr>		
			<tr>
				<td colspan="4">
					<table width="100%" cellpadding="4" cellspacing="0" border="1" style="border-collapse:collapse 1px solid black">
						<tr>
							<td width="30px">No</td>
							<td>POSISI / JABATAN</td>
							<td>JUMLAH</td>
							<td>TGl. MULAI KERJA</td>
							<td>KETERANGAN</td>
						</tr>
						<tr>
							<td>1.</td>
							<td><?= $a_data['posisikaryawan']; ?></td>
							<td><?= $a_data['jmldibutuhkan']; ?></td>
							<td><?= CStr::formatDate($a_data['tglaktifbekerja']); ?></td>
							<td></td>
						</tr>
					</table>
				</td>
			</tr>			
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>	
			<tr>
				<td><strong>B.</strong></td>
				<td colspan="3"><strong>PERSYARATAN UMUM</strong></td>
			</tr>		
			<tr>
				<td>1.</td>
				<td width="300px">Pendidikan</td>
				<td width="20px">:</td>
				<td><?= $a_data['namapendidikan']; ?></td>
			</tr>		
			<tr>
				<td>2.</td>
				<td>Usia / Jenis Kelamin</td>
				<td>:</td>
				<td>min <?= $a_data['syaratusiamin']; ?> th. max <?= $a_data['syaratusiamax']; ?> th. / <?= $a_data['jeniskelamin'] == 'L' ? 'Laki-laki' : ($a_data['jeniskelamin'] == 'P' ? 'Perempuan' : ''); ?></td>
			</tr>		
			<tr>
				<td>3.</td>
				<td>Alasan Permintaan</td>
				<td>:</td>
				<td><?= $a_data['alasanrekrutmen']; ?></td>
			</tr>	
			<tr>
				<td>4.</td>
				<td>Pengadaan Tenaga Kerja</td>
				<td>:</td>
				<td><?= $a_jenisrekrutmen[$a_data['jenisrekrutmen']]; ?></td>
			</tr>	
			<tr>
				<td>5.</td>
				<td>Sesuai Man Power Planning</td>
				<td>:</td>
				<td><?= $a_valid[$a_data['ismanpower']]; ?></td>
			</tr>	
			<tr>
				<td>6.</td>
				<td>Bidang Pengalaman</td>
				<td>:</td>
				<td><?= $a_data['syaratpengalaman']; ?></td>
			</tr>
			<tr>
				<td>7.</td>
				<td>Gaji</td>
				<td>:</td>
				<td>Rp. <?= CStr::formatNumber((int)$a_data['syaratgaji']); ?>&nbsp;</td>
			</tr>			
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>	
			<tr>
				<td><strong>C.</strong></td>
				<td colspan="3"><strong>PERSYARATAN TAMBAHAN</strong></td>
			</tr>	
			<tr>
				<td>1.</td>
				<td>Kepemimpinan</td>
				<td>:</td>
				<td><?= $a_data['syaratkepemimpinan']; ?></td>
			</tr>
			<tr>
				<td>2.</td>
				<td>Ketelitian Kerja</td>
				<td>:</td>
				<td><?= $a_ketelitian[$a_data['syaratketelitian']]; ?></td>
			</tr>
			<tr>
				<td>3.</td>
				<td>Kecepatan Kerja</td>
				<td>:</td>
				<td><?= $a_kecepatan[$a_data['syaratkecepatan']]; ?></td>
			</tr>
			<tr>
				<td>4.</td>
				<td>Kecerdasan</td>
				<td>:</td>
				<td><?= $a_kecerdasan[trim($a_data['syaratkecerdasan'])]; ?></td>
			</tr>
			<tr>
				<td>5.</td>
				<td>Kontak Sosial</td>
				<td>:</td>
				<td><?= $a_sosial[$a_data['syaratsosial']]; ?></td>
			</tr>
			<tr>
				<td>6.</td>
				<td>Keterampilan</td>
				<td>:</td>
				<td><?= $a_data['syaratketrampilan']; ?></td>
			</tr>
			<tr>
				<td>7.</td>
				<td>Bahasa</td>
				<td>:</td>
				<td><?= $a_data['syaratbahasa']; ?></td>
			</tr>
			<tr>
				<td>8.</td>
				<td>Uraian Singkat Tugas & Tanggung Jawab</td>
				<td>:</td>
				<td><?= $a_data['tugaskaryawan']; ?></td>
			</tr>
			<tr>
				<td>9.</td>
				<td>Apakah perlu job test / interview oleh bagian yang bersangkutan ?</td>
				<td>:</td>
				<td>Ya/Tidak</td>
			</tr>
			<tr>
				<td>10.</td>
				<td>Persyaratan Lain</td>
				<td>:</td>
				<td><?= $a_data['syaratlain']; ?></td>
			</tr>
		</table>
		<br />
		
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td colspan="2">Jakarta, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;<?= date('Y'); ?></td>
			</tr>
			<tr>
				<td align="center">Pengusul</td>
				<td align="center">Menyetujui, </td>
			</tr>
			<tr>
				<td colspan="2">
					<table width="100%" cellpadding="4" cellspacing="0" border="1" style="border-collapse:collapse 1px solid black">
						<tr height="100px">
							<td colspan="2" width="400px">&nbsp;</td>
							<td colspan="2" width="400px">&nbsp;</td>
						</tr>
						<tr>
							<td align="center">Kabag / Kaprodi / Wadek</td>
							<td align="center">Ka. Dept / Dekan</td>
							<td align="center">YPKB</td>
							<td align="center">Dept. Kepegawaian</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br />
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td colspan="2">Diterima kembali oleh Unit Dept. Kepegawaian : </td>
			</tr>
			<tr>
				<td width="200px">Tanggal</td>
				<td>:</td>
			</tr>
			<tr>
				<td>Diterima Oleh</td>
				<td>:</td>
			</tr>
		</table>
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>