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
	$p_file = 'gajipemindahanbuku_struktural_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Rekapitulasi Gaji Struktural';
	$p_col = 8;
	
	$namaunit = $p_model::getNamaUnit($conn,$r_kodeunit);
	
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
	
	$sqljenis .= " and h.idhubkerja in ('CP','HT') ";
	//mendapatkan data gaji
    $a_laporan = $p_model::repLapPindahBukuStruk($conn,$r_periode,$r_kodeunit, $sqljenis);
		
	$a_data = $a_laporan['data'];
	$a_unit = $a_laporan['unit'];
	$namaperiode = $a_laporan['namaperiode'];
	$a_ttd = $a_laporan['ttd'];
	$a_jns = $a_laporan['jenis'];
	$a_pot = $a_laporan['potongan'];
	$p_title = 'Daftar Gaji Karyawan '.$namaunit.'<br />';
	
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
					<th align="center" rowspan="2">JABATAN AKADEMIK/ ADMINISTRATIF</th>
					<th align="center" rowspan="2">GAJI</th>
					<th align="center" colspan="<?= count($a_jns)?>">POTONGAN</th>
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
				$totgaji=0;$totalbuku=0;$totalsaldo=0;$pph=0;
				$i=0;
				
				if(count($a_unit) > 0){
					foreach($a_unit as $unit => $keyunit){
						$isunit=0;
						$subtotgaji=0;$subtotalbuku=0;$subtotalsaldo=0;$subpph=0;
						foreach($keyunit as $key => $val){ 
							foreach($a_data[$val] as $row){
								$subtotgaji += $row['gajibruto'];
								if($row['norekening'] != '' and ($row['istunda'] != 'Y' or $row['istunda'] =='')){
									$subtotalbuku += $row['gajiditerima'];
								}
								else if($row['istunda'] == 'Y' or $row['norekening'] == ''){
									$subtotalsaldo += $row['gajiditerima'];
								}
								
								$subpph += $row['pph'];
								
								if($isunit==0){
							?>
								<tr>
									<td></td>
									<td></td>
									<td width="250"><strong><?= $row['namaunit']?></strong></td>
									<td></td>
									<?
										if(count($a_jns)>0){
											foreach($a_jns as $key=>$val){
												$subtotpotongan[$key]=0;
									?>
									<td></td>
									<?}}?>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							<? $isunit=1;
							}?>
								<tr>
									<td><?= ++$i?></td>
									<td><?= $row['nik']?></td>
									<td width="250"><?= $row['namalengkap']?></td>
									<td><?= $row['jabatanstruktural']?></td>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($row['gajibruto'],0,$dot) ?></td>
									<?
										if(count($a_jns)>0){
											foreach($a_jns as $key=>$val){
												$totpotongan[$key] += $a_pot[$row['idpegawai']][$key];
												$subtotpotongan[$key] += $a_pot[$row['idpegawai']][$key];
									?>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($a_pot[$row['idpegawai']][$key],0,$dot)?></td>
									<?}}?>
									<td style="border-right:1px solid black;" align="right"><?= ($row['norekening'] != '' and ($row['istunda'] != 'Y' or $row['istunda'] =='')) ? CStr::formatNumber($row['gajiditerima'],0,$dot) : '' ?></td>
									<td style="border-right:1px solid black;" align="right"><?= ($row['istunda'] == 'Y' or $row['norekening'] == '') ? CStr::formatNumber($row['gajiditerima'],0,$dot) : '' ?></td>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($row['pph'],0,$dot) ?></td>
								</tr>
					<?}}
					?>
					<tr>
						<td colspan="4" style="border:1px solid black;" align="center"><strong>Sub Total</strong></td>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtotgaji,0,$dot) ?></strong></td>
						<?
							if(count($a_jns)>0){
								foreach($a_jns as $key=>$val){
						?>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtotpotongan[$key],0,$dot)?></strong></td>
						<?}}?>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtotalbuku,0,$dot)?></strong></td>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtotalsaldo,0,$dot)?></strong></td>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subpph,0,$dot)?></strong></td>
					</tr>
				<?
						$totgaji += $subtotgaji;
						$totalbuku += $subtotalbuku;
						$totalsaldo += $subtotalsaldo;
						$pph += $subpph;
				}?>
				<tr>
					<td colspan="4" style="border:1px solid black;" align="center"><strong>TOTAL</strong></td>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totgaji,0,$dot) ?></strong></td>
					<?
						if(count($a_jns)>0){
							foreach($a_jns as $key=>$val){
					?>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totpotongan[$key],0,$dot)?></strong></td>
					<?}}?>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totalbuku,0,$dot)?></strong></td>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totalsaldo,0,$dot)?></strong></td>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($pph,0,$dot)?></strong></td>
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
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
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