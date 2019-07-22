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
	$p_file = 'rekapgajihonorer_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Rekapitulasi Gaji Honorer';
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
    	$a_laporan = $p_model::repLapRekapGajiHonorer($conn,$r_periode,$r_kodeunit, $sqljenis);
	$a_data = $a_laporan['list'];
	$a_ttd = $a_laporan['ttd'];
	$namaperiode = $a_laporan['namaperiode'];
		
	$p_title = 'Rekapitulasi Gaji Honorer Pegawai Universitas Esa Unggul <br />';
	
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
		<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" border="1">
			<thead>
				<tr bgcolor = "gray">
					<th align="center"><b style = "color:#FFFFFF">NO</b></th>
					<th align="center"><b style = "color:#FFFFFF">ID</b></th>
					<th align="center"><b style = "color:#FFFFFF">NAMA</b></th>
					<th align="center"><b style = "color:#FFFFFF">UNIT KERJA</b></th>
					<th align="center"><b style = "color:#FFFFFF">PENDIDIKAN</b></th>
					<th align="center"><b style = "color:#FFFFFF">TARIF</b></th>
					<th align="center"><b style = "color:#FFFFFF">JUMLAH HADIR</b></th>
					<th align="center"><b style = "color:#FFFFFF">GAJI DITERIMA</b></th>
				</tr>
			</thead>
			<tbody>
			<? 	
				$totalsaldo=0;$pph=0;
				$tottunj = array();
				if (count($a_data) >0) {
					$i=0;
					foreach($a_data as $row){ 
						$i++;
						$totalsaldo += $row['gajiditerima'];
			?>
				<tr>
					<td align="right"><?= $i; ?></td>
					<td><?= $row['idpegawai']; ?></td>
					<td><?= $row['namalengkap']; ?></td>
					<td><?= $row['namaunit']; ?></td>
					<td><?= $row['namapendidikan']; ?></td>
					<td align="right"><?= CStr::formatNumber($row['tarif'],0,$dot); ?></td>
					<td align="center"><?= $row['jum']; ?></td>
					<td align="right"><?= CStr::formatNumber($row['gajiditerima'],0,$dot); ?></td>
				</tr>
				<? } ?>
				<tr>
					<td colspan="7" align="center"><strong>TOTAL</strong></td>
					<td align="right"><strong><?= CStr::formatNumber($totalsaldo,0,$dot); ?></strong></td>
				</tr>
				<? }else{ ?>
				<tr>
					<td colspan="8" align="center">Data tidak ditemukan</td>
				</tr>
				<? } ?>
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
