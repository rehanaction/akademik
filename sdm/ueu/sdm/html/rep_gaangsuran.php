<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_bulan = CStr::removeSpecial($_REQUEST['bulan']);
	$r_periode = $r_tahun.str_pad($r_bulan, 2, "0", STR_PAD_LEFT);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	$r_jenis = $_POST['jenis'];
	
	require_once(Route::getModelPath('pinjaman'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_file = 'angpinjaman'.$r_kodeunit;
	$p_model = 'mPinjaman';
	$p_window = 'Rekapitulasi Permohonan Pot. Pinjaman';
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
    $a_laporan = $p_model::rekapPermohonanPot($conn,$r_periode,$r_kodeunit, $sqljenis);
	$a_data = $a_laporan['data'];
	$a_unit = $a_laporan['unit'];
	$namaperiode = $a_laporan['namaperiode'];
	$kepada = $a_laporan['kepada'];
	
	$a_jenispinjaman = $p_model::filterJenisPinjaman($conn,$where);
	$a_nominalpinjaman = $p_model::getNominalAngsuran($conn,$r_periode,$sqljenis);
	
	$p_title = 'Daftar Angsuran Pinjaman Pegawai<br />';
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
		<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" border="0">
			<tr>
				<td>Kepada Yth,</td>
			</tr>
			<tr>
				<td><?= $kepada['jabkepegawaian'];?></td>
			</tr>
			<tr>
				<td>Universitas Esa Unggul</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td><b>up.Bp.Ibu <?= $kepada['kepegawaian'];?></b></td>
			</tr>
			<tr>
				<td>Dengan Hormat,</td>
			</tr>
			<tr>
				<td>Bersama ini kami mengharapkan bantuannya untuk memotong Gaji Karyawan yang tersebut dibawah ini sesuai dengan jumlah angsuran yang tertera di kolom jumlah angsuran.</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td>Adapun daftar pemotongan Gaji tersebut adalah sebagai berikut : </td>
			</tr>
		</table>
		<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" style="border-collapse:collapse;border:1px solid black" class="tabel">
			<thead>
				<tr>
					<th align="center">No</th>
					<th align="center">Nama</th>
					<th align="center">Keterangan</th>
					<?
						if(count($a_jenispinjaman)>0){
							foreach($a_jenispinjaman as $jns){?>
								<th align="center" width="100">Angsuran <?= $jns?></th>	
							<?}
						}
					?>
					<th align="center">Total</th>
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
									<td width="250"><strong><?= $row['namaunit']?></strong></td>
									<td></td>
									<?
										if(count($a_jenispinjaman)>0){
											foreach($a_jenispinjaman as $jns){
									?>
											<td style="border-right:1px solid black;"></td>
									<?
											}
										}
									?>
									<td></td>
								</tr>
							<? $isunit=1;
							}?>
								<tr>
									<td style="border-right:1px solid black;" align="center"><?= ++$i; ?></td>
									<td style="border-right:1px solid black;"><?= $row['namalengkap']; ?></td>
									<td style="border-right:1px solid black;"></td>
									<?
										if(count($a_jenispinjaman)>0){
											$totangsuranpeg=0;
											foreach($a_jenispinjaman as $jns=>$val){
									?>
											<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($a_nominalpinjaman[$row['idpeminjam']][$jns],0,$dot)?></td>
									<?
											$totangsuranpeg += $a_nominalpinjaman[$row['idpeminjam']][$jns];
											$subtotangsuranunit[$row['idunitanggaran']][$jns] += $a_nominalpinjaman[$row['idpeminjam']][$jns];
											}
										}
									?>
									<td style="border-right:1px solid black;" align="right"><?= CStr::formatNumber($totangsuranpeg,0,$dot)?></td>
								</tr>
					<?}}?>
					<tr>
						<td colspan="3" style="border:1px solid black;" align="center"><strong>Sub Total</strong></td>
						<?
						if(count($a_jenispinjaman)>0){
							foreach($a_jenispinjaman as $jns=>$key){
								
						?>
								<td style="border:1px solid black;" align="right"><?= CStr::formatNumber($subtotangsuranunit[$unit][$jns],0,$dot)?></td>
						<?
								$totunitangsuran[$unit] += $subtotangsuranunit[$unit][$jns];
								$tottotal[$jns] += $subtotangsuranunit[$unit][$jns];
							}
						}
						?>
						<td style="border:1px solid black;" align="right"><strong><?= CStr::formatNumber($totunitangsuran[$unit],0,$dot)?></strong></td>
					</tr>
				<?}?>
				<tr>
					<td colspan="3" style="border:1px solid black;" align="center"><strong>TOTAL</strong></td>
					<?
					if(count($a_jenispinjaman)>0){
						foreach($a_jenispinjaman as $jns=>$key){
					?>
							<td style="border:1px solid black;" align="right"><?= CStr::formatNumber($tottotal[$jns],0,$dot)?></td>
					<?
						$total += $tottotal[$jns];
						}
					}
					?>
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