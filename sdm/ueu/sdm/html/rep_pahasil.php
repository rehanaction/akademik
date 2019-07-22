<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_pegawai = CStr::removeSpecial($_POST['key']);
	$r_periode = CStr::removeSpecial($_POST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('pa'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 8;
	$p_file = 'rekappa_'.$r_unit;
	$p_model = 'mPa';
	$p_window = 'Hasil Penilaian Kinerja';
	
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
	
    $a_laporan = $p_model::getRaportPA($conn,$r_periode,$r_pegawai);
	
	$a_bobot = $a_laporan['bobot'];
	$a_biodata = $a_laporan['data'];
	$a_jenispenilai = $a_laporan['jenispenilai'];
	$a_bobotsubj = $a_laporan['bobotsubj'];
	$a_bobotobj = $a_laporan['bobotobj'];
	$a_nilaisubj = $a_laporan['nilaisubj'];
	$namaperiode = $a_laporan['periode'];
		
	$p_title = 'Laporan Hasil Penilaian Kinerja <br />
				'.$namaperiode;
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
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br><br><br>
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td width="150px"><strong>Nama</strong></td>
				<td>: <?= $a_biodata['namalengkap']; ?>
			</tr>
			<tr>
				<td><strong>Eselon</strong></td>
				<td>: <?= $a_biodata['namaeselon']; ?> <em>(Eselon pada saat periode penilaian)</em>
			</tr>
			<tr>
				<td><strong>Jabatan</strong></td>
				<td>: <?= $a_biodata['jabatanstruktural']; ?>
			</tr>
			<tr>
				<td><strong>Unit Kerja</strong></td>
				<td>: <?= $a_biodata['namaunit']; ?>
			</tr>
			<tr>
				<td><strong>Kategori</strong></td>
				<td>: <?= $a_biodata['kategori']; ?>
			</tr>
		</table>
		<br><br>
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td width="50px"><strong>I</strong></td>
				<td><strong>Hasil Penilaian Subjektif</strong></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
				<? if (count($a_jenispenilai) > 0){	?>
					<table width="<?= $p_tbwidth-400; ?>" border="0" cellpadding="4" cellspacing="0">
						<? $i = 1; 
						$totalsubj = 0;
						foreach($a_jenispenilai as $kode => $namajenis){ 
							$totalsubj += $a_nilaisubj['nilai'][$kode]; 
						?>
						<tr>
							<td width="20px" align="right"><?= $i++; ?>.</td>
							<td width="100px"><?= $namajenis; ?></td>
							<td width="20px">:</td>
							<td width="80px" align="right"><?= $a_nilaisubj['rata'][$kode]; ?></td>
							<td width="20px" align="center">x</td>
							<td width="80px" align="right"><?= $a_bobotsubj[$kode]; ?> %</td>
							<td width="20px">=</td>
							<td align="right" width="80px"><?= $a_nilaisubj['nilai'][$kode]; ?></td>
						</tr>
						<? } ?>
						<tr>
							<td colspan="3">&nbsp;</td>
							<td colspan="3" align="right">Total</td>
							<td>=</td>
							<td align="right" style="border-top: 1px solid #EAEAEA;"><?= $totalsubj; ?></td>
						</tr>
					</table>
				<? } 
				?>
				<br />
				<table width="<?= $p_tbwidth-200; ?>" border="0" cellpadding="4" cellspacing="0">
					<tr style="background:yellow">
						<td width="200px">(<strong><span style="color:blue">Nilai Subjektif</span> <?= $a_bobot['bobotsubjektif']?>%</strong>) :</td>
						<td width="80px" align="right"><?= $totalsubj; ?></td>
						<td width="80px" align="center">x</td>
						<td align="right" width="100px"><?= $a_bobot['bobotsubjektif']?>%</td>
						<td width="20px">=</td>
						<td align="right"><?= $a_biodata['nilaisubyektif']; ?></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="50px"><strong>II</strong></td>
				<td><strong>Hasil Penilaian Objektif</strong></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
				<table width="<?= $p_tbwidth-300; ?>" border="0" cellpadding="4" cellspacing="0">
					<tr>
						<td width="50px"><strong>A.</strong></td>
						<td><strong>Nilai Objektif - I ( <?= $a_bobotobj['ob1']; ?>% )</strong></td>
						<td align="right"><?= $a_biodata['nilaiob1']; ?></td>
						<td width="80px" align="center">x</td>
						<td><?= $a_bobotobj['ob1']; ?>%</td>
						<td width="20px">=
						<? $nilaiob1 = $a_biodata['nilaiob1'] * ($a_bobotobj['ob1']/100); ?>
						</td>
						<td align="right"><?= $nilaiob1; ?></td>
					</tr>
					<tr>
						<td width="50px"><strong>B.</strong></td>
						<td><strong>Nilai Objektif - II ( <?= $a_bobotobj['ob2']; ?>% )</strong> <em>(Kehadiran)</em></td>
						<td align="right"><?= $a_biodata['nilaiob2']; ?></td>
						<td width="80px" align="center">x</td>
						<td><?= $a_bobotobj['ob2']; ?>%</td>
						<td width="20px">=</td>
						<? $nilaiob2 = $a_biodata['nilaiob2'] * ($a_bobotobj['ob2']/100); ?>
						<td align="right"><?= $nilaiob2; ?></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right" style="border-top: 1px solid #EAEAEA;"><?= $nilaiobj = $nilaiob1 + $nilaiob2 ; ?></td>
					</tr>
				</table>
				<br />
				<table width="<?= $p_tbwidth-200; ?>" border="0" cellpadding="4" cellspacing="0">
					<tr style="background:yellow">
						<td width="300px"><strong><span style="color:blue">Nilai Objektif</span> (Obj.I + II x <?= $a_bobot['bobotobjektif']?>%)</strong> :</td>
						<? 
							$totalobj = $nilaiobj * ($a_bobot['bobotobjektif']/100);
						?>
						<td><?= $nilaiobj; ?></td>
						<td width="80px" align="center">x</td>
						<td align="right" width="100px"><?= $a_bobot['bobotobjektif']?>%</td>
						<td width="20px">=</td>
						<td align="right"><?= $a_biodata['nilaiobyektif']; ?></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="50px"><strong>III</strong></td>
				<td><strong>Hasil Akhir</strong></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
				<table width="<?= $p_tbwidth-200; ?>" border="0" cellpadding="4" cellspacing="0">
					<tr style="background:yellow">
						<td width="300px"><strong><span style="color:blue">Total Nilai Akhir</span> (Subj. + Obj)</strong> :</td>
						<td><?= $a_biodata['nilaisubyektif']; ?></td>
						<td width="80px" align="center">+</td>
						<td align="right" width="100px"><?= $a_biodata['nilaiobyektif'] ?></td>
						<td width="20px">=</td>
						<td align="right"><?= $a_biodata['nilaiakhir'] ?>&nbsp;&nbsp;&nbsp;<strong>( <?= $a_biodata['kategorinilai']?> )</strong></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>