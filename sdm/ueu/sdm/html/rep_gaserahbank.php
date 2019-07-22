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
	$p_file = 'gajibank_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Pemindahan Buku Gaji';
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
	
	if(empty($r_jenis))
		$sqljenis = '';
	else if(count($r_jenis) == 1) {
		if(is_array($r_jenis)) $r_jenis = $r_jenis[0];
		$sqljenis = "and p.idjenispegawai = '".CStr::cAlphaNum($r_jenis)."' ";
	}
	else {
		for($i=0;$i<count($r_jenis);$i++)
			$r_jenis[$i] = CStr::cAlphaNum($r_jenis[$i]);
		$i_jenispeg = implode("','",$r_jenis);
		$sqljenis = "and p.idjenispegawai in ('$i_jenispeg') ";
	}
	
	$sqljenis .= " and p.idhubkerja in ('CP','HT') ";	
	//mendapatkan data gaji
    $a_laporan = $p_model::repLapSerahBank($conn,$r_periode,$r_kodeunit, $sqljenis);
	$a_pegawai = $a_laporan['datapegawai'];
	$a_rekening = $a_laporan['rekening'];
	$potrekening = $a_laporan['datapotrekening'];
	$namaperiode = $a_laporan['namaperiode'];
	$a_ttd = $a_laporan['ttd'];
		
	$p_title = 'Daftar Gaji Karyawan Universitas Esa Unggul <br />';
	if(!empty($a_data['namaunit']))
		$p_title .= 'Unit '.$a_data['namaunit'].'<br />';
	
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
					<th align="center">NO</th>
					<th align="center">NIP</th>
					<th align="center">NOREK</th>
					<th align="center">NIS TAB/GI</th>
					<th align="center">KODE CIB</th>
					<th align="center">KODE A/O</th>
					<th align="center">NAMA</th>
					<th align="center">ALAMAT</th>
					<th align="center">GAJI</th>
				</tr>
			</thead>
			<tbody>
				<? $total=0;
					if (count($a_pegawai) >0) {
					$i=0;
						foreach($a_pegawai as $pegawai){
							$total += $pegawai['gajiditerima'];
							?>
							<tr>
								<td align="center"><?= ++$i; ?></td>
								<td align="center"><?= $pegawai['nik']; ?></td>
								<td align="center"><?= $pegawai['norekening']; ?></td>
								<td align="center">1</td>
								<td align="center"><td>
								<td><?= $pegawai['anrekening']; ?></td>
								<td><?= $pegawai['alamat']; ?></td>
								<td align="right"><?= CStr::formatNumber($pegawai['gajiditerima'],0,$dot) ?></td>
							</tr>
							<? }
							foreach($a_rekening as $rekening){
							$total += $potrekening[$rekening['kodepotongan']];
							?>
							<tr>
								<td align="right"><strong><?= ++$i; ?></strong></td>
								<td align="center"></td>
								<td align="center"><strong><?= $rekening['norekening'];?></strong></td>
								<td align="center"><strong>1</strong></td>
								<td align="center"><td>
								<td><strong><?= $rekening['anrekening']; ?></strong></td>
								<td></td>
								<td align="right"><strong><?= CStr::formatNumber($potrekening[$rekening['kodepotongan']],0,$dot)?></strong></td>
							</tr>
							<? }
				}else{ ?>
				<tr>
					<td colspan="<?= $p_col?>" align="center">Data tidak ditemukan</td>
				</tr>
				<? } ?>
				<tr>
					<td colspan="<?= $p_col-1?>" align="center"><strong>TOTAL</strong></td>
					<td><strong><?= CStr::formatNumber($total,0,$dot); ?><strong></td>
				</tr>
			</tbody>
		</table>
		<br />
		<table width="<?= $p_tbwidth ?>" cellpadding="4">
			<tr>
				<td align="center" colspan="2">Jakarta, <?= CStr::formatDateInd(date("Y-m-d")); ?></td>
			</tr>
			<tr>
				<td align="center">PENDATANGAN I</td>
				<td align="center">PENDATANGAN II</td>
			</tr>
			<tr height="30">
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr height="30">
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td align="center"><strong>(<?= $a_ttd['rektor']; ?>)</strong></td>
				<td align="center"><strong>(<?= $a_ttd['yayasan']; ?>)</strong></td>
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