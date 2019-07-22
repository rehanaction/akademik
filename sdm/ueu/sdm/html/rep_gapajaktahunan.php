<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_file = 'rekappajak_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Rekapitulasi Pajak Tahunan';
	$p_col = 9;
	
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
		
	//mendapatkan data gaji
    $a_laporan = $p_model::repLapPajakTahunan($conn,$r_tahun,$r_kodeunit);
	$a_data = $a_laporan['data'];
	$a_gaji = $a_laporan['gaji'];
	$a_pph = $a_laporan['pph'];
	
	$p_title = 'Rekap Pajak Tahunan Universitas Esa Unggul <br />';
	if(!empty($a_data['namaunit']))
		$p_title .= 'Unit '.$a_data['namaunit'].'<br />';
	
	$p_title .= 'Tahun '.$r_tahun;

?>
<html>
<head>
	<title><?= $p_window; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<style>
	div,td,th {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	}
	td,th { border:1px solic black }
	</style>
</head>
<body>
	<div align="center">
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" cellpadding="4" border="1" cellspacing="0" style="border-collapse:collapse">
			<thead>
				<tr>
					<th rowspan="3" align="center">NO</th>
					<th rowspan="3" align="center">NIP</th>
					<th rowspan="3" align="center">NAMA</th>
					<th colspan="24" align="center">BULAN</th>
					<th rowspan="3" align="center">TOTAL GAJI</th>
					<th rowspan="3" align="center">TOTAL PPh. 21</th>
				</tr>
				<tr>
					<?
						for($bln=1;$bln<=12;$bln++){?>
							<th align="center" colspan="2"><?= Date::indoMonth($bln,true)?></th>
						<?}
					?>
				</tr>
				<tr>
					<?
						for($bln=1;$bln<=12;$bln++){?>
							<th align="center">Gaji</th>
							<th align="center">PPh. 21</th>
						<?}
					?>
				</tr>
			</thead>
			<? $total=0;
				if (count($a_data) >0) {
				$i=0;
				foreach($a_data as $row){
			?>
			<tbody>
				<tr>
					<td align="center"><?= ++$i; ?></td>
					<td align="center"><?= $row['nik']; ?></td>
					<td><?= $row['namapegawai']; ?></td>
					<?
						$totgajipeg =0;
						$totpphpeg =0;
						$totgajibulan[] = 0;
						$totpphbulan[] = 0;
						for($bln=1;$bln<=12;$bln++){
							$bulan=str_pad($bln,2,'0',STR_PAD_LEFT);
							?>
								<td align="right"><?= CStr::formatNumber($a_gaji[$bulan][$row['idpegawai']],0,$dot) ?></td>
								<td align="right"><?= CStr::formatNumber($a_pph[$bulan][$row['idpegawai']],0,$dot) ?></td>
							<?
							$totgajipeg += $a_gaji[$bulan][$row['idpegawai']];
							$totpphpeg += $a_pph[$bulan][$row['idpegawai']];
							$totgajibulan[$bulan] += $a_gaji[$bulan][$row['idpegawai']];
							$totpphbulan[$bulan] += $a_pph[$bulan][$row['idpegawai']];
						}
					?>
					<td align="right"><strong><?= CStr::formatNumber($totgajipeg,0,$dot) ?><strong></td>
					<td align="right"><strong><?= CStr::formatNumber($totpphpeg,0,$dot) ?><strong></td>
				</tr>
				<? }}else{ ?>
				<tr>
					<td colspan="29" align="center">Data tidak ditemukan</td>
				</tr>
				<? } ?>
				<tr>
					<td colspan="3" align="right"><strong>Total</strong></td>
					<?
					for($bln=1;$bln<=12;$bln++){
							$bulan=str_pad($bln,2,'0',STR_PAD_LEFT);
					?>
								<td align="right"><strong><?= CStr::formatNumber($totgajibulan[$bulan],0,$dot); ?><strong></td>
								<td align="right"><strong><?= CStr::formatNumber($totpphbulan[$bulan],0,$dot); ?><strong></td>
								
					<?
						$totgaji += $totgajibulan[$bulan]; 
						$totpph += $totpphbulan[$bulan]; 
					}?>
					<td><strong><?= CStr::formatNumber($totgaji,0,$dot); ?><strong></td>
					<td><strong><?= CStr::formatNumber($totpph,0,$dot); ?><strong></td>
				</tr>
			</tbody>
		</table>
		<br />
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>