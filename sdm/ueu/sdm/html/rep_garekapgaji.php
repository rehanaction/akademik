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
	$p_file = 'rekapgajipegawai_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Rekapitulasi Gaji Pegawai';
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
    $a_laporan = $p_model::repLapRekapGaji($conn,$r_periode,$r_kodeunit, $sqljenis);
	$a_data = $a_laporan['data'];
	$a_unit = $a_laporan['unit'];
	$namaperiode = $a_laporan['namaperiode'];
	$a_ttd = $a_laporan['ttd'];
	$a_jns = $a_laporan['jenis'];
	$a_tunj = $a_laporan['tunjangan'];
		
	$p_title = 'Rekapitulasi Gaji Pegawai Universitas Esa Unggul <br />';
	
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
					<th align="center" rowspan="2">JABATAN STRUKTURAL</th>
					<th align="center" rowspan="2">PENDIDIKAN TERAKHIR</th>
					<th align="center" rowspan="2" style="padding-left:20px;padding-right:20px">TGL. MASUK</th>
					<th align="center" rowspan="2">JABATAN AKADEMIK</th>
					<th align="center" rowspan="2">GAJI POKOK</th>
					<th align="center" colspan="<?= count($a_jns)?>">TUNJANGAN</th>
					<th align="center" rowspan="2">PPh. 21</th>
					<th align="center" rowspan="2">GAJI DITERIMA</th>
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
				$totgapok=0;$totalgaji=0;$totalpph=0;
				$i=0;$isunit=0;
				
				if(count($a_unit) > 0){
					foreach($a_unit as $unit => $keyunit){
						$totgapok += $subtotgapok;
						$totalgaji += $subtotalgaji;
						$totalpph += $subpph;
						$isunit=0;
						$subtotgapok=0;$subtotalgaji=0;$subpph=0;
						foreach($keyunit as $key => $val){ 
							foreach($a_data[$val] as $row){
									$subtotgapok += $row['gapok'];
									$subtotalgaji+= $row['gajiditerima'];
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
												$subtottunj[$key]=0;
									?>
									<td></td>
									<?}}?>
									<td></td>
									<td></td>
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
									<td><?= $row['namapendidikan']?></td>
									<td><?= CStr::formatDateInd($row['tglmasuk'],false)?></td>
									<td><?= $row['jabatanfungsional']?></td>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($row['gapok'],0,$dot) ?></td>
									<?
										if(count($a_jns)>0){
											foreach($a_jns as $key=>$val){
												$tottunjangan[$key] += $a_tunj[$row['idpegawai']][$key];
												$subtottunj[$key] += $a_tunj[$row['idpegawai']][$key];
									?>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($a_tunj[$row['idpegawai']][$key],0,$dot)?></td>
									<?}}?>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($row['pph'],0,$dot) ?></td>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($row['gajiditerima'],0,$dot) ?></td>
								</tr>
					<?}}
					?>
					<tr>
						<td colspan="7" style="border:1px solid black;" align="center"><strong>Sub Total</strong></td>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtotgapok,0,$dot) ?></strong></td>
						<?
							if(count($a_jns)>0){
								foreach($a_jns as $key=>$val){
						?>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtottunj[$key],0,$dot)?></strong></td>
						<?}}?>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subpph,0,$dot)?></strong></td>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($subtotalgaji,0,$dot)?></strong></td>
					</tr>
				<?}?>
				<tr>
					<td colspan="7" style="border:1px solid black;" align="center"><strong>TOTAL</strong></td>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totgapok,0,$dot) ?></strong></td>
					<?
						if(count($a_jns)>0){
							foreach($a_jns as $key=>$val){
					?>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($tottunjangan[$key],0,$dot)?></strong></td>
					<?}}?>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totalpph,0,$dot)?></strong></td>
					<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totalgaji,0,$dot)?></strong></td>
				</tr>
				<?}?>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>