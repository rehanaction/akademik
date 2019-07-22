<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	$r_jenis = $_POST['jenis'];
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_file = 'rekaplembur_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Rekapitulasi Lembur';
	$p_col = 8;
	
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
	
	if(empty($r_jenis))
		$sqljenis = '';
	else if(count($r_jenis) == 1) {
		if(is_array($r_jenis)) $r_jenis = $r_jenis[0];
		$sqljenis = "and j.idjenispegawai = '".CStr::cAlphaNum($r_jenis)."' ";
	}
	else {
		for($i=0;$i<count($r_jenis);$i++)
			$r_jenis[$i] = CStr::cAlphaNum($r_jenis[$i]);
		$i_jenispeg = implode("','",$r_jenis);
		$sqljenis = "and j.idjenispegawai in ('$i_jenispeg') ";
	}
		
	//mendapatkan data gaji
    $a_laporan = $p_model::repLapRekapLembur($conn,$r_periode,$r_kodeunit, $sqljenis);
	$a_data = $a_laporan['data'];
	$a_ttd = $a_laporan['ttd'];
	$namaperiode = $a_laporan['namaperiode'];
	
	$p_title = 'Rekapitulasi Lembur Pegawai<br />';
	
	$p_title .= 'Periode '.$namaperiode;
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
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" style="border-collapse:collapse;border:1px solid black">
			<thead>
				<tr>
					<th align="center" style="border-collapse:collapse;border:1px solid black">NO</th>
					<th align="center" style="border-collapse:collapse;border:1px solid black">NAMA</th>
					<th align="center" style="border-collapse:collapse;border:1px solid black">TANGGAL</th>
					<th align="center" style="border-collapse:collapse;border:1px solid black">STATUS</th>
					<th align="center" style="border-collapse:collapse;border:1px solid black">JUMLAH JAM</th>
					<th align="center" style="border-collapse:collapse;border:1px solid black">TOTAL JAM</th>
					<th align="center" style="border-collapse:collapse;border:1px solid black">LEMBUR PER JAM</th>
					<th align="center" style="border-collapse:collapse;border:1px solid black">JUMLAH</th>
				</tr>
			</thead>
			<tbody>
			<? 	
				if(count($a_data) > 0){
					$noid=0;
					foreach($a_data as $row){
						$total += $row['lembur'];			
						if($unit != $row['idunit']){
							$no=1;
			?>
				<tr>
					<td style="border-right:1px solid black; border-bottom:1px solid black; padding-left:40px;" colspan="8"><u><b><?= $row['namaunit']?></b></u></td>
				</tr>
			<? 
						}
						if($idpegawai != $row['idpegawai']){
							$id=1;
							$noid++;
			?>
				<tr>
					<td style="border:1px solid black;" align="center" rowspan="<?= $a_laporan['rowspan'][$row['idpegawai']]+1;?>" valign="top"><?= $noid; ?></td>
					<td style="border:1px solid black;" rowspan="<?= $a_laporan['rowspan'][$row['idpegawai']]+1;?>" valign="top"><?= $row['namapegawai']; ?></td>
			<?
						}
						else
							echo '<tr>';
			?>
					<td style="border-right:1px solid black;"><?= CStr::formatDateTimeInd($row['tgllembur'],true,true);?></td>
					<td style="border-right:1px solid black;" align="center"><?= $row['jenislembur'];?></td>
					<td style="border-right:1px solid black;" align="center"><?= CStr::formatNumber($row['jmljam'],0,$dot);?></td>
					<td style="border-right:1px solid black;" align="center"><?= $row['totaljam'];?></td>
					<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($row['lemburjam'],0,$dot);?></td>
					<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($row['lembur'],0,$dot);?></td>
				</tr>
			<?
						if($id == $a_laporan['rowspan'][$row['idpegawai']]){
					
			?>
				<tr>
					<td style="border:1px solid black;" colspan="5" align="center">Total Lembur</td>
					<td style="border:1px solid black;" align="right"><?= CStr::formatNumber($a_laporan['total'][$row['idpegawai']],0,$dot);?></td>
				</tr>
			<?	
						}

						if($no == $a_laporan['rowspanunit'][$row['idunit']]){
					
			?>
				<tr>
					<td style="border:1px solid black;" colspan="7" align="center">Total Lembur Unit</td>
					<td style="border:1px solid black;" align="right"><?= CStr::formatNumber($a_laporan['totalunit'][$row['idunit']],0,$dot);?></td>
				</tr>
			<?	
						}

						$id++;
						$no++;
						$unit = $row['idunit'];
						$idpegawai = $row['idpegawai'];

					}
				}
				else{ 
			?>
				<tr>
					<td style="border-right:1px solid black;" colspan="<?= $p_col?>" align="center">Data tidak ditemukan</td>
				</tr>
			<? } ?>
				<tr>
					<td style="border-right:1px solid black;border-top:1px solid black;" colspan="7" align="center"><strong>Total</strong></td>
					<td style="border:1px solid black;" align="right"><?= CStr::formatNumber($total,0,$dot)?></td>
				</tr>
			</tbody>
		</table>
		<br />
		<table width="<?= $p_tbwidth ?>" cellpadding="4">
			<tr>
				<td align="center" colspan="2">Mengetahui,</td>
				<td width="200px"></td>
				<td>Jakarta, <?= CStr::formatDateInd(date("Y-m-d")); ?></td>
			</tr>
			<tr>
				<td></td>
				<td>Universitas Esa Unggul</td>
				<td>&nbsp;</td>
				<td>Universitas Esa Unggul</td>
			</tr>
			<tr height="30">
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr height="30">
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td><u><strong><?= $a_ttd['keuangan']; ?></strong></u></td>
				<td>&nbsp;</td>
				<td><u><strong><?= $a_ttd['kepegawaian']; ?></strong></u></td>
			</tr>
			<tr>
				<td></td>
				<td><?= $a_ttd['jabkeuangan']; ?></td>
				<td>&nbsp;</td>
				<td><?= $a_ttd['jabkepegawaian']; ?></td>
			</tr>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
