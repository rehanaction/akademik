<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_bulan = CStr::removeSpecial($_REQUEST['bulan']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('rekrutmen'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 9;
	$p_file = 'rekapprogress_'.$r_kodeunit;
	$p_model = 'mRekrutmen';
	$p_window = 'Rekapitulasi Progres Penerimaan Pegawai';
	
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
	
	$a_sumkandidat = $p_model::sumKandidat($conn);
	$a_sumkandidatint = $p_model::sumKandidatInt($conn);
	$a_jenisrekrutmen = $p_model::jenisRekrutmen();
	
    $a_data = $p_model::getLapProgress($conn,$r_kodeunit,$r_tahun,$r_bulan);
	
	$rs = $a_data['list'];
	$a_jumlolos = $a_data['terima'];
	
	$p_title = 'Rekapitulasi Progres Penerimaan Pegawai <br />
				Unit '.$a_data['namaunit'].'<br />
				Periode '.Date::indoMonth($r_bulan).' '.$r_tahun;
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
				<th rowspan="2" style = "color:#FFFFFF">No</th>
				<th rowspan="2" style = "color:#FFFFFF">Unit</th>
				<th rowspan="2" style = "color:#FFFFFF">Jenis Rekrutmen</th>
				<th rowspan="2" style = "color:#FFFFFF">Jenis Pegawai</th>
				<th rowspan="2" style = "color:#FFFFFF">Posisi</th>
				<th rowspan="2" style = "color:#FFFFFF">Tgl Rekruitmen</th>
				<th colspan="3" style = "color:#FFFFFF">Jumlah</th>
			</tr>
			<tr bgcolor ="gray">
				<th style = "color:#FFFFFF">Kebutuhan</th>
				<th style = "color:#FFFFFF">Kandidat</th>
				<th style = "color:#FFFFFF">Diterima</th>
			</tr>	
			<? $i=0;
				while ($row = $rs->FetchRow()){ $i++;
			?>
			<tr>
				<td><?= $i; ?></td>
				<td><?= $row['namaunit']; ?></td>
				<td><?= $a_jenisrekrutmen[$row['jenisrekrutmen']]; ?></td>
				<td><?= $row['jenispegawai']; ?></td>
				<td><?= $row['posisikaryawan']; ?></td>
				<td><?= CStr::formatDateInd($row['tglrekrutmen']); ?></td>
				<td align="right"><?= $row['jmldibutuhkan']; ?></td>
				<td align="right"><?= $row['jenisrekrutmen'] == 'B' ? $a_sumkandidat[$row['idrekrutmen']] : $a_sumkandidatint[$row['idrekrutmen']]; ?></td>
				<td align="right"><?= $row['jenisrekrutmen'] == 'B' ? $a_jumlolos[$row['idrekrutmen']] : ''; ?></td>
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