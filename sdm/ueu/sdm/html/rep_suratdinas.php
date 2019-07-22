<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('dinas'));
	
	// definisi variable halaman	
	$p_tbwidth = 600;
	$p_col = 13;
	$p_file = 'suratdinas_'.$r_key;
	$p_model = 'mDinas';
	$p_window = 'Surat Tugas Dinas';
	
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
		
    $a_rep = $p_model::repSuratDinas($conn,$r_key);
    $a_data = $a_rep['list'];
    $a_ttd = $a_rep['ttd'];
	
	list($nomor,$romawi,$tahun) = explode('/',$a_data['nosurat']);
	$nomorsurat = $nomor.'/ESA UNGGUL/STD/'.$romawi.'/'.$tahun;
	
	if(SDM::isPegawai()){
		$r_pegawai = Modul::getIDPegawai();
		if ($r_pegawai != $a_data['pegditunjuk'])
			Route::navigate('home');
	}
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
		
		<strong>SURAT TUGAS DINAS <br />
		<hr style="width:200px" />
		Nomor : <?= $nomorsurat; ?>
		</strong>
		<br />
		<br />
		<br />
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td colspan="3">Bersama ini diberikan tugas kepada :</td>
			</tr>			
			<tr>
				<td width="30px"></td>
				<td width="100px">Nama</td>
				<td>: <?= $a_data['namapegawai']; ?></td>
			</tr>		
			<tr>
				<td></td>
				<td valign="top">Jabatan / Unit</td>
				<td valign="top">: <?= $a_data['jabatanstruktural'].' / '.$a_data['namaunit']; ?></td>
			</tr>		
			<tr>
				<td></td>
				<td>Alamat</td>
				<td>: <?= $a_data['alamatpegawai']; ?></td>
			</tr>
		</table>
		<br />
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td colspan="3">Untuk Melaksanakan tugas dinas :</td>
			</tr>			
			<tr>
				<td width="30px"></td>
				<td width="100px">Dalam Rangka</td>
				<td>: <?= $a_data['dalamrangka']; ?>
			</tr>		
			<tr>
				<td></td>
				<td>Instansi</td>
				<td>: <?= $a_data['instansi']; ?></td>
			</tr>		
			<tr>
				<td></td>
				<td>Alamat</td>
				<td>: <?= $a_data['alamat']; ?></td>
			</tr>		
			<tr>
				<td></td>
				<td>Tanggal</td>
				<td>: <?= CStr::formatDateInd($a_data['tglpergi']).' s/d '.CStr::formatDateInd($a_data['tglpulang']); ?></td>
			</tr>
		</table>
		<br />
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td width="30%" align="center">Mengetahui,</td>
				<td width="30%" align="center">Yang Menugaskan,</td>
				<td width="30%" align="center">Jakarta, <?= CStr::formatDateInd($a_data['tglusulan']); ?></td>
			</tr>
			<tr>
				<td width="30%" align="center">&nbsp;</td>
				<td width="30%" align="center">&nbsp;</td>
				<td width="30%" align="center"><?= $a_ttd['jabkepegawaian']?></td>
			</tr>	
			<tr height="50px">
				<td></td>
				<td></td>
				<td></td>
			</tr>	
			<tr height="50px">
				<td></td>
				<td></td>
				<td></td>
			</tr>		
			<tr>
				<td><hr style="width:130px" /></td>
				<td><hr style="width:130px" /></td>
				<td><hr style="width:130px" /></td>
			</tr>	
			<tr>
				<td align="center"><?= $a_data['instansi']; ?></td>
				<td align="center"><?= $a_data['namapejabat']?></td>
				<td align="center"><?= $a_ttd['kepegawaian']?></td>
			</tr>	
		</table>
		<br />
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td>Catatan :</td>
			</tr>
			<tr>
				<td>1. Surat Tugas Dinas ini agar dikembalikan kepada <?= $a_ttd['unitkepegawaian']?>, setelah tugas diselesaikan diketahui instansi yang dikunjungi</td>
			</tr>
			<tr>
				<td>2. Bila Tugas Dinas merupakan Pelatihan/Seminar/Lokakarya atau sejenisnya, agar melampirkan Formulir Persetujuan mengikuti pelatihan. (Formulir dapat diambil di <?= $a_ttd['unitkepegawaian']?>)</td>
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