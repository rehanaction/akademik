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
	$p_file = 'lemburbank_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Rekap Lembur Pegawai';
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
		$sqljenis = "and idjenispegawai = '".CStr::cAlphaNum($r_jenis)."' ";
	}
	else {
		for($i=0;$i<count($r_jenis);$i++)
			$r_jenis[$i] = CStr::cAlphaNum($r_jenis[$i]);
		$i_jenispeg = implode("','",$r_jenis);
		$sqljenis = "and idjenispegawai in ('$i_jenispeg') ";
	}
		
	//mendapatkan data gaji
    $a_laporan = $p_model::repLapPindahLembur($conn,$r_periode,$r_kodeunit, $sqljenis);
	$a_data = $a_laporan['data'];
	$namaperiode = $a_laporan['namaperiode'];
	$a_ttd = $a_laporan['ttd'];
		
	$p_title = 'Rekap Lembur Pegawai Universitas Esa Unggul <br />';
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
					<th align="center">JENIS TAB/GIRO</th>
					<th align="center">KODE CIB</th>
					<th align="center">KODE A/O</th>
					<th align="center">NAMA</th>
					<th align="center">ALAMAT</th>
					<th align="center">TOTAL LEMBUR</th>
				</tr>
			</thead>
			<? $total=0;
				if (count($a_data) >0) {
				$i=0;
				foreach($a_data as $row){
					$total += $row['totallembur'];
			?>
			<tbody>
				<tr>
					<td align="right"><?= ++$i; ?></td>
					<td align="center"><?= $row['nik']; ?></td>
					<td align="center"><?= $row['norekening']; ?></td>
					<td align="center">1</td>
					<td align="center"><td>
					<td><?= $row['anrekening']?></td>
					<td></td>
					<td align="right"><?= CStr::formatNumber($row['totallembur'],0,$dot) ?></td>
				</tr>
				<? }}else{ ?>
				<tr>
					<td colspan="<?= $p_col?>" align="center">Data tidak ditemukan</td>
				</tr>
				<? } ?>
				<tr>
					<td colspan="<?= $p_col-1?>" align="right"><strong>Total</strong></td>
					<td align="right"><strong><?= CStr::formatNumber($total,0,$dot); ?><strong></td>
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