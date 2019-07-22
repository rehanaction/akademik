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
	$p_tbwidth = 1200;
	$p_file = 'rekappotongan_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'DAFTAR POTONGAN KARYAWAN/DOSEN';
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
		$sqljenis = "and h.idjenispegawai = '".CStr::cAlphaNum($r_jenis)."' ";
	}
	else {
		for($i=0;$i<count($r_jenis);$i++)
			$r_jenis[$i] = CStr::cAlphaNum($r_jenis[$i]);
		$i_jenispeg = implode("','",$r_jenis);
		$sqljenis = "and h.idjenispegawai in ('$i_jenispeg') ";
	}
		
	//mendapatkan data gaji
    $a_laporan = $p_model::rekapLapPotKehadiran($conn,$r_periode,$r_kodeunit, $sqljenis);
	$a_data = $a_laporan['data'];
	$a_unit = $a_laporan['unit'];
	$namaperiode = $a_laporan['namaperiode'];
	$namaunit = $a_laporan['namaunit'];
	$p_title = 'DAFTAR POTONGAN KARYAWAN/DOSEN<br />';
	
	$p_title .= $namaunit.'<br/>';
	$p_title .= 'Periode '.$namaperiode;
	
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
	thead, th { border-right:1px solid black;border-bottom:1px solid black; }
	.tabel td { border-right:1px solid black;}
	</style>
</head>
<body>
	<div align="center">
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" style="border-collapse:collapse;border:1px solid black" class="tabel">
			<thead>
				<tr>
					<th align="center">NO</th>
					<th align="center">NIP</th>
					<th align="center">NAMA</th>
					<th align="center">JABATAN AKADEMIK/ ADMINISTRATIF</th>
					<th align="center">TRANSPORT</th>
					<th align="center">KEHADIRAN</th>
					<th align="center">TOTAL POTONGAN</th>
				</tr>
			</thead>
			<tbody>
			<? 	
				$tottransport=0;$totkehadiran=0;$total=0;
				$i=0;
				if(count($a_unit) > 0){
					foreach($a_unit as $unit => $keyunit){
						$isunit=0;
						$subtottransport=0;$subtotkehadiran=0;$subtotal=0;
						foreach($keyunit as $key => $val){
							$totpotpeg=0;
							foreach($a_data[$val] as $row){
							if($isunit==0){
							?>
								<tr>
									<td></td>
									<td></td>
									<td width="250"><strong><?= $row['namaunit']?></strong></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							<? $isunit=1;
							}
							$totpotpeg=$row['pottransport']+$row['potkehadiran'];
							?>
								<tr>
									<td><?= ++$i?></td>
									<td><?= $row['nik']?></td>
									<td width="250"><?= $row['namalengkap']?></td>
									<td><?= $row['jabatanstruktural']?></td>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($row['pottransport'],0,$dot) ?></td>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($row['potkehadiran'],0,$dot) ?></td>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($totpotpeg,0,$dot) ?></td>
						
								</tr>
					<?
						$subtottransport += $row['pottransport'];
						$subtotkehadiran += $row['potkehadiran'];
						$subtotal += $totpotpeg;	
					}
					}
					?>
					<tr>
						<td colspan="4" style="border:1px solid black;" align="center"><strong>Sub Total</strong></td>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtottransport,0,$dot) ?></strong></td>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtotkehadiran,0,$dot)?></strong></td>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtotal,0,$dot)?></strong></td>
					</tr>
				<?
				$tottransport += $subtottransport;
				$totkehadiran += $subtotkehadiran;
				$total += $subtotal;
				}
				?>
				<tr>
					<td colspan="4" style="border:1px solid black;" align="center"><strong>TOTAL</strong></td>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($tottransport,0,$dot) ?></strong></td>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totkehadiran,0,$dot)?></strong></td>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($total,0,$dot)?></strong></td>
				</tr>
				<?}?>
			</tbody>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>