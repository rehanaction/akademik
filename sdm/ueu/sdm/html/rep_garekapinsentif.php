<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_file = 'rekapinsentif_'.$r_periode;
	$p_model = 'mGaji';
	$p_window = 'Rekapitulasi Insentif Dosen';
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
		
	//mendapatkan data gaji
    $a_laporan = $p_model::repRekapInsentif($conn,$r_periode,$r_kodeunit);
	$a_data = $a_laporan['data'];
	$a_unit = $a_laporan['unit'];
	$namaperiode = $a_laporan['namaperiode'];
	$a_ttd = $a_laporan['ttd'];
	$a_jns = $a_laporan['jenis'];
	$a_insentif = $a_laporan['insentif'];
	
	$p_title = 'Daftar Gaji Insentif Universitas Esa Unggul<br />';
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
					<th align="center" rowspan="2">NO</th>
					<th align="center" rowspan="2">NIP</th>
					<th align="center" rowspan="2">NAMA</th>
					<th align="center" colspan="<?= count($a_jns)?>">INSENTIF</th>
					<th align="center" rowspan="2">PEMINDAHBUKUAN</th>
					<th align="center" rowspan="2">SALDO</th>
					<th align="center" rowspan="2">PPh. 21</th>
				</tr>
				<tr>
					
					<?
						if(count($a_jns)>0){
					
							foreach($a_jns as $key=>$val){
					?>
					<th align="center"><?= strtoupper($val)?></th>
					<?}}?>
				</tr>
			</thead>
			<tbody>
			<? 	
				$i=0;		
				if(count($a_unit) > 0){
					foreach($a_unit as $unit => $keyunit){
						$isunit=0;
						foreach($keyunit as $key => $val){ 
							foreach($a_data[$val] as $row){
							if($isunit==0){
							?>
								<tr>
									<td></td>
									<td></td>
									<td width="250"><strong><?= $row['namaunit']?></strong></td>
									<?
										if(count($a_jns)>0){
											foreach($a_jns as $jns=>$val){
									?>
											<td style="border-right:1px solid black;"></td>
									<?
											}
										}
									?>
									<td style="border-right:1px solid black;"></td>
									<td style="border-right:1px solid black;"></td>
									<td style="border-right:1px solid black;"></td>
								</tr>
							<? $isunit=1;
							}?>
								<tr>
									<td style="border-right:1px solid black;" align="center"><?= ++$i; ?></td>
									<td style="border-right:1px solid black;"><?= $row['nik']; ?></td>
									<td style="border-right:1px solid black;"><?= $row['namalengkap']; ?></td>
									<?
										if(count($a_jns)>0){
											$totinsentifpegbuku=0;
											$totinsentifpegsaldo=0;
											foreach($a_jns as $jns=>$val){
									?>
											<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($a_insentif[$row['idpegawai']][$jns],0,$dot)?></td>
									<?
											if($row['norekening'] != ''){
												$totinsentifpegbuku += $a_insentif[$row['idpegawai']][$jns];
												$totunitinsentifbuku[$row['idunitanggaran']] += $a_insentif[$row['idpegawai']][$jns];
											}
											if($row['norekening'] == ''){
												$totinsentifpegsaldo += $a_insentif[$row['idpegawai']][$jns];
												$totunitinsentifsaldo[$row['idunitanggaran']] += $a_insentif[$row['idpegawai']][$jns];
											}
											$subtotinsentifunit[$row['idunitanggaran']][$jns] += $a_insentif[$row['idpegawai']][$jns];
											}
										}
									?>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($totinsentifpegbuku,0,$dot)?></td>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($totinsentifpegsaldo,0,$dot)?></td>
									<td style="border-right:1px solid black;" align="right">&nbsp;</td>
								</tr>
					<?}}?>
					<tr>
						<td colspan="3" style="border:1px solid black;" align="center"><strong>Sub Total</strong></td>
						<?
						if(count($a_jns)>0){
							foreach($a_jns as $jns=>$val){
								
						?>
								<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtotinsentifunit[$unit][$jns])?></strong>&nbsp;</td>
						<?
							
							$totinsentif[$jns] += $subtotinsentifunit[$unit][$jns];
							
							}
							$totalbuku += $totunitinsentifbuku[$unit];
							$totalsaldo += $totunitinsentifsaldo[$unit];
						}
						?>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totunitinsentifbuku[$unit],0,$dot)?></strong></td>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totunitinsentifsaldo[$unit],0,$dot)?></strong></td>
						<td style="border:1px solid black;" align="right"><strong></strong>&nbsp;</td>
					</tr>
				<?}?>
				<tr>
					<td colspan="3" style="border:1px solid black;" align="center"><strong>TOTAL</strong></td>
					<?
					if(count($a_jns)>0){
						foreach($a_jns as $jns=>$val){
					?>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totinsentif[$jns],0,$dot)?></strong></td>
					<?
						}
					}
					?>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totalbuku,0,$dot)?></strong></td>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totalsaldo,0,$dot)?></strong></td>
					<td style="border:1px solid black;" align="right"><strong></strong>&nbsp;</td>
				</tr>
				<?}?>
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
				<td>Yayasan Pendidikan Kemala Bangsa</td>
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
				<td><u><strong><?= $a_ttd['yayasan']; ?></strong></u></td>
				<td><u><strong><?= $a_ttd['keuangan']; ?></strong></u></td>
				<td>&nbsp;</td>
				<td><u><strong><?= $a_ttd['kepegawaian']; ?></strong></u></td>
			</tr>
			<tr>
				<td><?= $a_ttd['jabyayasan']; ?></td>
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