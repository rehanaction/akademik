<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_idpegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_file = 'sliplembur_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Slip Lembur Gaji Pegawai';
	
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
	
	//bila diunduh bentuk doc atau excel, koma rupiah dihilangkan
	$dot = true;
	if($r_format == 'doc' or $r_format == 'xls')
		$dot = false;
		
	//mendapatkan data lembur
	$a_lembur = $p_model::repSlipLembur($conn,$r_periode,$r_kodeunit,$r_idpegawai);
	
	$rs = $a_lembur['list'];
	$a_data = $a_lembur['data'];
	
	$p_title = 'Slip Lembur Pegawai <br />';
	if(!empty($a_lembur['namaunit']))
		$p_title .= 'Unit '.$a_lembur['namaunit'].'<br />';
	
	$p_title .= 'Periode '.$a_lembur['namaperiode'];
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
<? while($row = $rs->FetchRow()){?>
	<div align="center" style="page-break-after:always">
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" style="border:1px solid" cellpadding="4" cellspacing="0">
			<tr>
				<td width="150px">Nama</td>
				<td width="50px">:</td>
				<td><b><?= $row['namapegawai'] ?></b></td>
			</tr>
			<tr>
				<td>Unit</td>
				<td>:</td>
				<td><?= $row['namaunit'] ?></td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3"><b>Lembur di Hari Kerja</b></td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="100%" cellpadding="4" cellspacing="0" align="center" border="1" style="border-collapse:collapse">
						<tr bgcolor="#CCCCCC">
							<td align="center">No.</td>
							<td align="center">Tanggal</td>
							<td align="center">Jam Datang</td>
							<td align="center">Jam Pulang</td>
							<td align="center">Jam Diakui</td>
						</tr>
						<? 
							if (count($a_data[$row['idpegawai']]['H']['tanggal']) > 0){
								$i = 0;
								foreach($a_data[$row['idpegawai']]['H']['tanggal'] as $inc => $date){
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG';
						?>
						<tr class="<?= $rowstyle ?>">
							<td align="right"><?= ++$i; ?></td>
							<td align="center"><?= CStr::formatDateInd($date); ?></td>
							<td align="center"><?= CStr::formatJam($a_data[$row['idpegawai']]['H']['jamdatang'][$inc]); ?></td>
							<td align="center"><?= CStr::formatJam($a_data[$row['idpegawai']]['H']['jampulang'][$inc]); ?></td>
							<td align="center"><?= number_format($a_data[$row['idpegawai']]['H']['totlembur'][$inc],2); ?></td>
						</tr>
						<? }}else{ ?>
						<tr>
							<td colspan="5" align="center">Data tidak ditemukan</td>
						</tr>
						<? } ?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3"><b>Lembur di Hari Libur</b></td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="100%" cellpadding="4" cellspacing="0" align="center" border="1" style="border-collapse:collapse">
						<tr bgcolor="#CCCCCC">
							<td align="center">No.</td>
							<td align="center">Tanggal</td>
							<td align="center">Jam Datang</td>
							<td align="center">Jam Pulang</td>
							<td align="center">Jam Diakui</td>
						</tr>
						<? 
							if (count($a_data[$row['idpegawai']]['HL']['tanggal']) > 0){
								$i = 0;
								foreach($a_data[$row['idpegawai']]['HL']['tanggal'] as $inc => $date){
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG';
						?>
						<tr class="<?= $rowstyle ?>">
							<td align="right"><?= ++$i; ?></td>
							<td align="center"><?= CStr::formatDateInd($date); ?></td>
							<td align="center"><?= CStr::formatJam($a_data[$row['idpegawai']]['HL']['jamdatang'][$inc]); ?></td>
							<td align="center"><?= CStr::formatJam($a_data[$row['idpegawai']]['HL']['jampulang'][$inc]); ?></td>
							<td align="center"><?= number_format($a_data[$row['idpegawai']]['HL']['totlembur'][$inc],2); ?></td>
						</tr>
						<? }}else{ ?>
						<tr>
							<td colspan="5" align="center">Data tidak ditemukan</td>
						</tr>
						<? } ?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td><b>Upah Lembur</b></td>
				<td><b>: Rp.</b></td>
				<td><b><?= CStr::formatNumber($a_data[$row['idpegawai']]['upahlembur'],0,$dot) ?></b></td>
			</tr>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
<?}?>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>