<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_bulan = CStr::removeSpecial($_REQUEST['bulan']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('presensi'));
	
	// definisi variable halaman	
	$p_tbwidth = 1250;
	$p_col = 13;
	$p_file = 'rekapitulasipresensi_'.$r_kodeunit;
	$p_model = 'mPresensi';
	$p_window = 'Rekapitulasi Presensi Karyawan';
	
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
	
    $a_data = $p_model::repRekapPresensi($conn,$r_kodeunit,$r_tahun,$r_bulan);
	
	$rs = $a_data['list'];
	
	$p_title = 'Rekapitulasi Presensi Karyawan<br />
				Unit '.$a_data['namaunit'].'<br />
				Periode '.Date::indoMonth($r_bulan).' '.$r_tahun;
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
		<? require_once($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<tr bgcolor = "gray">
				<th><b style = "color:#FFFFFF">No</b></th>
				<th><b style = "color:#FFFFFF">NIP</b></th>
				<th><b style = "color:#FFFFFF">Nama Pegawai</b></th>
				<th><b style = "color:#FFFFFF">Unit Kerja</b></th>
				<th width="75px"><b style = "color:#FFFFFF">Hadir</b></th>
				<th width="75px"><b style = "color:#FFFFFF">Sakit</b></th>
				<th width="75px"><b style = "color:#FFFFFF">Izin</b></th>
				<th width="75px"><b style = "color:#FFFFFF">Alpa</b></th>
				<th width="75px"><b style = "color:#FFFFFF">Cuti</b></th>
				<th width="75px"><b style = "color:#FFFFFF">Dinas</b></th>
				<th width="100px"><b style = "color:#FFFFFF">Tugas Belajar</b></th>
				<th width="75px"><b style = "color:#FFFFFF">Terlambat</b></th>
				<th width="100px"><b style = "color:#FFFFFF">Hadir Libur</b></th>
			</tr>
			
			<? $i=0;
				while ($row = $rs->FetchRow()){ $i++;
					$hadir += $row['hadir'];
					$sakit += $row['sakit'];
					$izin += $row['izin'];
					$alpa += $row['alpa'];
					$cuti += $row['cuti'];
					$dinas += $row['dinas'];
					$tugasbelajar += $row['tugasbelajar'];
					$terlambat += $row['terlambat'];
					$hadirlibur += $row['hadirlibur'];
			?>
			
			<tr>
				<td><?= $i; ?></td>
				<td><?= $row['nik']; ?></td>
				<td><?= $row['namapegawai']; ?></td>
				<td><?= $row['namaunit']; ?></td>
				<td align="center"><?= $row['hadir']; ?></td>
				<td align="center"><?= $row['sakit']; ?></td>
				<td align="center"><?= $row['izin']; ?></td>
				<td align="center"><?= $row['alpa']; ?></td>
				<td align="center"><?= $row['cuti']; ?></td>
				<td align="center"><?= $row['dinas']; ?></td>
				<td align="center"><?= $row['tugasbelajar']; ?></td>
				<td align="center"><?= $row['terlambat']; ?></td>
				<td align="center"><?= $row['hadirlibur']; ?></td>
			</tr>
			
			<? }
				if ($i == 0){
			?>
			
			<tr>
				<td colspan="<?= $p_col; ?>" align="center">Data tidak ditemukan</td>
			</tr>
			<? } ?>
			<tr>
				<td colspan="4" align="center"><strong>Jumlah</strong></td>
				<td align="center"><strong><?= $hadir; ?></strong></td>
				<td align="center"><strong><?= $sakit; ?></strong></td>
				<td align="center"><strong><?= $izin; ?></strong></td>
				<td align="center"><strong><?= $alpa; ?></strong></td>
				<td align="center"><strong><?= $cuti; ?></strong></td>
				<td align="center"><strong><?= $dinas; ?></strong></td>
				<td align="center"><strong><?= $tugasbelajar; ?></strong></td>
				<td align="center"><strong><?= $terlambat; ?></strong></td>
				<td align="center"><strong><?= $hadirlibur; ?></strong></td>
			</tr>
		</table>
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
